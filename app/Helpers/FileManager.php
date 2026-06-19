<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FileManager
{
    public static function saveResume($file, $user_id)
    {
        if (!$file) {
            return null;
        }
        return $file->store("resume/{$user_id}", 'files');
    }

    public static function storeChunkTemp(UploadedFile $file, string $fileUuid, int $chunkIndex): string
    {
        $name = "chunk_{$chunkIndex}.part";
        Storage::disk('digital_files')->putFileAs("tmp/chunks/{$fileUuid}", $file, $name);
        return $name;
    }

    /**
     * 🔥 ترکیب فوق‌العاده بهینه تکه‌ها با سیستم همگام‌سازی دایرکتوری‌ها در ویندوز و لینوکس
     */
    public static function combineChunks(string $fileUuid, int $totalChunks, string $extension): string
    {
        $disk = Storage::disk('digital_files');
        $finalTempName = (string) Str::uuid() . '.' . $extension;

        // ۱. ابتدا با استفاده از امکانات لاراول مطمئن می‌شویم پوشه مقصد وجود دارد
        if (!$disk->exists('tmp/products')) {
            $disk->makeDirectory('tmp/products');
        }

        // ۲. ساخت مسیر مطلق متناسب با سیستم‌عامل
        $finalTempPath = $disk->path("tmp/products/{$finalTempName}");
        if (DIRECTORY_SEPARATOR === '\\') {
            $finalTempPath = str_replace('/', '\\', $finalTempPath);
        }

        // ۳. باز کردن فایل اصلی برای استریم
        $outputStream = fopen($finalTempPath, 'wb');
        if ($outputStream === false) {
            throw new \Exception("امکان ساخت فایل نهایی در مسیر سرور وجود ندارد. دسترسی پوشه storage را بررسی کنید.");
        }

        // ۴. چسباندن تکه‌ها
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkRelativePath = "tmp/chunks/{$fileUuid}/chunk_{$i}.part";

            if (!$disk->exists($chunkRelativePath)) {
                fclose($outputStream);
                @unlink($finalTempPath);
                throw new \Exception("تکه شماره {$i} یافت نشد. فرآیند آپلود منقطع شده است.");
            }

            $chunkPath = $disk->path($chunkRelativePath);
            if (DIRECTORY_SEPARATOR === '\\') {
                $chunkPath = str_replace('/', '\\', $chunkPath);
            }

            $inputStream = fopen($chunkPath, 'rb');
            if ($inputStream) {
                stream_copy_to_stream($inputStream, $outputStream);
                fclose($inputStream);
            } else {
                fclose($outputStream);
                @unlink($finalTempPath);
                throw new \Exception("خطا در خواندن تکه باینری شماره {$i}");
            }
        }

        fclose($outputStream);

        // ۵. یک بررسی امنیتی نهایی: آیا واقعاً فایل با موفقیت روی هارد دیسک ویندوز ایجاد شد؟
        if (!file_exists($finalTempPath) || filesize($finalTempPath) === 0) {
            throw new \Exception("فایل نهایی پس از ترکیب روی دیسک ذخیره نشد یا حجم آن صفر است.");
        }

        return $finalTempName;
    }

    public static function cleanTrackedChunks(string $fileUuid): void
    {
        $disk = Storage::disk('digital_files');
        $folder = "tmp/chunks/{$fileUuid}";

        if ($disk->exists($folder)) {
            $disk->deleteDirectory($folder);
        }
    }

    public static function tempPath(string $tempName): string
    {
        $path = Storage::disk('digital_files')->path("tmp/products/{$tempName}");
        return DIRECTORY_SEPARATOR === '\\' ? str_replace('/', '\\', $path) : $path;
    }

    public static function moveFromTemp(string $tempName, int $productId): string
    {
        $disk = Storage::disk('digital_files');
        $from = "tmp/products/{$tempName}";
        $to = "products/{$productId}/{$tempName}";

        if (!$disk->exists($from)) {
            throw new \Exception('فایل نهایی در پوشه موقت یافت نشد تا منتقل شود.');
        }

        // مطمئن شدن از وجود پوشه نهایی محصول
        if (!$disk->exists("products/{$productId}")) {
            $disk->makeDirectory("products/{$productId}");
        }

        $disk->copy($from, $to);
        $disk->delete($from);

        return $tempName;
    }

    public static function metadata(string $path, string $originalName, string $extension, string $hash): array
    {
        return [
            'original_name' => $originalName,
            'extension'      => $extension,
            'mime_type'      => self::realMime($path),
            'size'           => filesize($path),
            'sha256'         => $hash,
        ];
    }

    public static function realMime(string $path): string
    {
        if (!file_exists($path)) {
            return 'application/octet-stream';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        return $mime ?: 'application/octet-stream';
    }

    public static function storeChunkTempFromBinary(string $binaryData, string $fileUuid, int $chunkIndex): string
    {
        $disk = Storage::disk('digital_files');

        // مطمئن شدن از وجود پوشه چانک‌ها
        if (!$disk->exists("tmp/chunks/{$fileUuid}")) {
            $disk->makeDirectory("tmp/chunks/{$fileUuid}");
        }

        $name = "chunk_{$chunkIndex}.part";
        $path = "tmp/chunks/{$fileUuid}/{$name}";

        $disk->put($path, $binaryData);

        return $name;
    }

    public static function deleteDigitalFile(int $productId, string $fileName): void
    {
        $disk = Storage::disk('digital_files');
        $path = "products/{$productId}/{$fileName}";

        try {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
            Log::info('File deleted', ['product_id' => $productId, 'file' => $fileName]);
        } catch (\Throwable $e) {
            Log::error('File delete failed', ['product_id' => $productId, 'file' => $fileName, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
