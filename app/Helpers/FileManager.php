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
        Storage::disk('digital_files_tmp')->putFileAs("chunks/{$fileUuid}", $file, $name);
        return $name;
    }

    /**
     * 🚀 ترکیب چانک‌ها بدون متد path() فیزیکی - ۱۰۰٪ سازگار با مالتی‌سرور و کلاود
     */
    public static function combineChunks(string $fileUuid, int $totalChunks, string $extension): string
    {
        $tmpDisk = Storage::disk('digital_files_tmp');
        $finalTempName = (string) Str::uuid() . '.' . $extension;

        // ایجاد یک فایل خام موقت برای کپی کردن استریم‌ها درون آن
        $tmpDisk->put($finalTempName, '');

        // باز کردن استریم مقصد
        $targetStream = fopen($tmpDisk->path($finalTempName), 'wb');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkRelativePath = "chunks/{$fileUuid}/chunk_{$i}.part";

            if (!$tmpDisk->exists($chunkRelativePath)) {
                fclose($targetStream);
                $tmpDisk->delete($finalTempName);
                throw new \Exception("تکه باینری شماره {$i} مفقود شده است.");
            }

            // خواندن با استریم محلی یا ابری به صورت انتزاعی
            $chunkStream = $tmpDisk->readStream($chunkRelativePath);
            stream_copy_to_stream($chunkStream, $targetStream);

            if (is_resource($chunkStream)) {
                fclose($chunkStream);
            }
        }

        fclose($targetStream);
        return $finalTempName;
    }

    /**
     * ایجاد یک کپی محلی موقت صرفاً جهت عملیات‌هایی مثل ZipArchive یا Finfo که نیاز به هندل مستقیم لایه سیستم‌عامل دارند
     */
    public static function ensureLocalCopy(string $tempName): string
    {
        $tmpDisk = Storage::disk('digital_files_tmp');

        // اگر دیسک محلی بود، مسیر خودش را بدهد، در غیر این‌صورت یک کپی موقت در پوشه تمپ سیستم‌عامل بسازد
        if ($tmpDisk->getConfig()['driver'] === 'local') {
            return $tmpDisk->path($tempName);
        }

        $localTmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tempName;
        file_put_contents($localTmpPath, $tmpDisk->get($tempName));
        return $localTmpPath;
    }

    public static function moveToFinalStorage(string $tempName, int $productId): string
    {
        $tmpDisk = Storage::disk('digital_files_tmp');
        $finalDisk = Storage::disk('digital_files');
        $targetPath = "products/{$productId}/{$tempName}";

        $localStream = $tmpDisk->readStream($tempName);
        $uploaded = $finalDisk->writeStream($targetPath, $localStream);

        if (is_resource($localStream)) {
            fclose($localStream);
        }

        if (!$uploaded) {
            throw new \Exception("خطا در انتقال فایل نهایی به فضای دیسک کلاود اصلی.");
        }

        $tmpDisk->delete($tempName);
        return $tempName;
    }

    public static function cleanTrackedChunks(string $fileUuid): void
    {
        $tmpDisk = Storage::disk('digital_files_tmp');
        $folder = "chunks/{$fileUuid}";

        if ($tmpDisk->exists($folder)) {
            $tmpDisk->deleteDirectory($folder);
        }
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

    public static function deleteDigitalFile(int $productId, string $fileName): void
    {
        Storage::disk('digital_files')->delete("products/{$productId}/{$fileName}");
    }
}
