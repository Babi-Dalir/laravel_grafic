<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileManager;
use App\Services\FileValidation\ZipScannerService;
use Illuminate\Http\UploadedFile;

class ProductFileUploadService
{
    protected $zipScanner;

    public function __construct(ZipScannerService $zipScanner)
    {
        $this->zipScanner = $zipScanner;
    }

    /**
     * آپلود تکه فایلهای باینری خالص بدون افت سرعت و افزایش حجم شبکه
     */
    public function uploadBinaryChunk(
        UploadedFile $file,
        string $fileUuid,
        int $chunkIndex,
        int $totalChunks,
        string $originalName,
        Product $product,
        ?string $title = null
    ): ?ProductFile {

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedExtensions = config('uploads.allowed_extensions', [
            'dxf', 'png', 'jpg', 'jpeg', 'cdr', 'art', 'svg', 'webp', 'tiff',
            'stl', 'obj', '3ds', 'stp', 'step', 'zip', 'psd', 'ai', 'eps', 'pdf', 'ttf', 'otf'
        ]);

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new Exception('فرمت فایل انتخاب شده مجاز نیست.');
        }

        // ذخیره فیزیکی موقت چانک باینری
        FileManager::storeChunkTemp($file, $fileUuid, $chunkIndex);

        // اگر هنوز چانک‌های بعدی مانده است، به کار خود ادامه بده
        if ($chunkIndex + 1 < $totalChunks) {
            return null;
        }

        // چسباندن و اعتبارسنجی در گام پایانی آپلود چانک‌ها
        return DB::transaction(function () use ($fileUuid, $totalChunks, $originalName, $extension, $product, $title) {

            $tempName = FileManager::combineChunks($fileUuid, $totalChunks, $extension);
            $tempPath = FileManager::tempPath($tempName);

            try {
                // 🔒 بررسی حجم واقعی فایل سرهم شده روی دیسک
                $actualFileSize = filesize($tempPath);
                $maxFourGigabytes = 4294967296;

                if ($actualFileSize > $maxFourGigabytes) {
                    throw new Exception('حجم فایل نهایی فراتر از حد مجاز (۴ گیگابایت) است. لطفاً فایل را فشرده‌تر کنید.');
                }

                // ۱. بررسی هویت واقعی فایل بر اساس ساختار داخلی آن
                $realMime = FileManager::realMime($tempPath);

                if (!$this->isValidMimeForExtension($extension, $realMime)) {
                    throw new Exception('محتوای داخلی فایل با پسوند ظاهری آن مطابقت ندارد!');
                }

                // ۲. اسکن امنیتی فایل‌های زیپ
                if ($extension === 'zip') {
                    $this->zipScanner->scan($tempPath);
                }

                // ۳. هش فایل نهایی جهت اصالت‌سنجی
                $hash = hash_file('sha256', $tempPath);

                // ۴. بررسی تکراری نبودن فایل برای این محصول
                $exists = ProductFile::where([
                    'product_id' => $product->id,
                    'sha256' => $hash,
                ])->exists();

                if ($exists) {
                    throw new Exception('این فایل دقیقاً با همین محتویات قبلاً برای این محصول آپلود شده است.');
                }

                // ۵. استخراج متاداتا و مشخصات ساختاری فایل
                $metadata = FileManager::metadata($tempPath, $originalName, $extension, $hash);

                // ۶. انتقال به دایرکتوری نهایی و دائمی
                $storedName = FileManager::moveFromTemp($tempName, $product->id);

                // ۷. ثبت مشخصات در دیتابیس
                return ProductFile::create([
                    'product_id' => $product->id,
                    'title' => $title,
                    'stored_name' => $storedName,
                    'is_default' => !$product->files()->exists(),
                    ...$metadata,
                ]);

            } catch (\Throwable $e) {
                if (Storage::disk('digital_files')->exists("tmp/products/{$tempName}")) {
                    Storage::disk('digital_files')->delete("tmp/products/{$tempName}");
                }
                throw $e;
            } finally {
                FileManager::cleanTrackedChunks($fileUuid);
            }
        });
    }

    protected function isValidMimeForExtension(string $extension, string $mime): bool
    {
        $validMimes = [
            'jpg'  => ['image/jpeg', 'image/pjpeg', 'application/octet-stream'],
            'jpeg' => ['image/jpeg', 'image/pjpeg', 'application/octet-stream'],
            'png'  => ['image/png', 'image/x-png', 'application/octet-stream'],
            'webp' => ['image/webp', 'image/x-webp', 'application/octet-stream'],
            'tiff' => ['image/tiff', 'image/x-tiff', 'application/octet-stream'],
            'svg'  => ['image/svg+xml', 'application/xml', 'text/xml', 'text/plain', 'application/octet-stream'],
            'psd'  => ['image/vnd.adobe.photoshop', 'application/x-photoshop', 'image/psd', 'application/octet-stream'],
            'ai'   => ['application/postscript', 'application/pdf', 'application/vnd.adobe.illustrator', 'application/octet-stream'],
            'eps'  => ['application/postscript', 'image/x-eps', 'image/eps', 'application/octet-stream'],
            'cdr'  => ['application/cdr', 'application/coreldraw', 'image/cdr', 'application/x-cdr', 'zz-application/zz-winassoc-cdr', 'application/octet-stream'],
            'art'  => ['image/x-jg', 'application/octet-stream'],
            'pdf'  => ['application/pdf', 'application/x-pdf', 'application/acrobat', 'text/pdf', 'application/octet-stream'],
            'zip'  => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-zip', 'application/octet-stream'],
            'dxf'  => ['image/vnd.dxf', 'image/x-dxf', 'application/dxf', 'application/x-dxf', 'text/plain', 'application/octet-stream'],
            'stl'  => ['application/sla', 'application/stl', 'application/x-navistyle', 'application/octet-stream', 'text/plain'],
            'obj'  => ['text/plain', 'application/object', 'application/x-tgif', 'application/octet-stream'],
            '3ds'  => ['image/x-3ds', 'application/x-3ds', 'application/octet-stream'],
            'stp'  => ['application/step', 'application/octet-stream', 'text/plain'],
            'step' => ['application/step', 'application/octet-stream', 'text/plain'],
            'ttf'  => ['font/ttf', 'font/sfnt', 'application/x-font-ttf', 'application/x-font-truetype', 'application/octet-stream'],
            'otf'  => ['font/otf', 'font/sfnt', 'application/x-font-opentype', 'application/octet-stream'],
        ];

        if (!array_key_exists($extension, $validMimes)) {
            return false;
        }

        return in_array($mime, $validMimes[$extension], true);
    }

    public function delete(ProductFile $file)
    {
        $productId = $file->product_id;

        // حذف فایل اصلی
        FileManager::deleteDigitalFile($productId, $file->stored_name);
        $file->delete();

        $hasAnyFiles = ProductFile::query()->where('product_id', $productId)->exists();

        if (!$hasAnyFiles) {
            $disk = Storage::disk('digital_files');

            // ایمن‌سازی حذف دایرکتوری‌ها با try-catch
            try {
                $productDirectory = "products/{$productId}";
                if ($disk->exists($productDirectory)) {
                    $disk->deleteDirectory($productDirectory);
                }

                // هماهنگی با مسیرهای موقت (مطمئن شوید FileManager هم از همین مسیر استفاده می‌کند)
                $tmpDirectory = "tmp/products/{$productId}";
                if ($disk->exists($tmpDirectory)) {
                    $disk->deleteDirectory($tmpDirectory);
                }
            } catch (\Throwable $e) {
                Log::error("خطا در حذف دایرکتوری‌های محصول پس از حذف آخرین فایل: " . $e->getMessage());
            }
        }
    }
}
