<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\ProductFile;
use App\Helpers\FileManager;
use App\Events\ProductFileUploaded;

class ProductFileUploadService
{
    public function uploadBinaryChunk(
        $file,
        string $fileUuid,
        int $chunkIndex,
        int $totalChunks,
        string $originalName,
        Product $product,
        ?string $title = null
    ): bool {

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = config('uploads.allowed_extensions', [
            'dxf', 'png', 'jpg', 'jpeg', 'cdr', 'art', 'svg', 'webp', 'tiff',
            'stl', 'obj', '3ds', 'stp', 'step', 'zip', 'psd', 'ai', 'eps', 'pdf', 'ttf', 'otf'
        ]);

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new Exception('فرمت فایل انتخاب شده مجاز نیست.');
        }

        // ذخیره فیزیکی موقت چانک باینری روی سرور محلی
        FileManager::storeChunkTemp($file, $fileUuid, $chunkIndex);

        // اگر هنوز چانک‌های بعدی باقی مانده است، سیگنال موفقیت پارت را بفرست
        if ($chunkIndex + 1 < $totalChunks) {
            return false;
        }

        // کامباین کردن سریع چانک‌ها روی دیسک لوکال در آخرین پارت رسیده
        $tempName = FileManager::combineChunks($fileUuid, $totalChunks, $extension);

        // 🚀 شلیک رویداد معماری رویدادمحور جهت پردازش ناهمگام در صف و آزادسازی فوری کلاینت
        // خط زیر در آخرین متد کامباین آپلود بدین شکل بهینه‌سازی شد:
        event(new ProductFileUploaded($product->id, $tempName, $originalName, $title, $fileUuid));

        return true; // نشان‌دهنده اتمام دریافت کل چانک‌ها است
    }

    public function isValidMimeForExtension(string $extension, string $mime): bool
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

        return isset($validMimes[$extension]) && in_array($mime, $validMimes[$extension], true);
    }

    public function delete(ProductFile $file)
    {
        FileManager::deleteDigitalFile($file->product_id, $file->stored_name);
        $file->delete();
    }
}
