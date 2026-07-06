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
            'jpg'  => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png'  => ['image/png', 'image/x-png'],
            'webp' => ['image/webp', 'image/x-webp'],
            'tiff' => ['image/tiff', 'image/x-tiff'],

            'svg'  => ['image/svg+xml', 'application/xml', 'text/xml'],

            'pdf'  => ['application/pdf', 'application/x-pdf'],

            'zip'  => ['application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'],

            'psd'  => ['image/vnd.adobe.photoshop', 'application/x-photoshop', 'image/psd', 'application/photoshop'],

            // 🟢 حل باگ قطعی فایل‌های هوشمند AI که برپایه PDF خروجی می‌گیرند
            'ai'   => [
                'application/postscript',
                'application/vnd.adobe.illustrator',
                'application/pdf',
                'application/x-pdf'
            ],

            'eps'  => ['application/postscript', 'image/x-eps', 'image/eps', 'application/eps', 'application/x-eps'],

            'cdr'  => [
                'application/cdr',
                'application/coreldraw',
                'image/cdr',
                'application/x-cdr',
                'application/vnd.coreldraw',
                'application/x-coreldraw',
                'zz-application/zz-winassoc-cdr',
                'application/x-riff', // بسیار مهم برای خروجی‌های باینری کورل در لینوکس
            ],

            'art'  => ['image/x-jg'],

            'dxf'  => ['image/vnd.dxf', 'image/x-dxf', 'application/dxf', 'text/plain', 'text/csv'],

            'stl'  => ['application/sla', 'application/stl', 'text/plain', 'application/octet-stream'],

            'obj'  => ['text/plain', 'application/object', 'application/octet-stream'],

            '3ds'  => ['image/x-3ds', 'application/x-3ds', 'application/octet-stream'],

            'stp'  => ['application/step', 'text/plain', 'application/octet-stream'],
            'step' => ['application/step', 'text/plain', 'application/octet-stream'],

            'ttf'  => ['font/ttf', 'font/sfnt', 'application/x-font-ttf', 'application/x-font-truetype'],
            'otf'  => ['font/otf', 'font/sfnt', 'application/x-font-opentype'],
        ];

        if (!isset($validMimes[$extension])) {
            return false;
        }

        // ۱. تطابق صریح با آرایه جامع امنیتی بالا
        if (in_array($mime, $validMimes[$extension], true)) {
            return true;
        }

        // ۲. مکانیزم Fallback برای لایه‌های باینری ناشناخته لینوکس (فقط برای فایل‌های برداری و سه بعدی)
        $binaryFallbackAllowed = [
            'cdr', 'ai', 'psd', 'eps', 'obj', 'stl', '3ds', 'stp', 'step', 'dxf'
        ];

        $genericBinaryMimes = [
            'application/octet-stream',
            'application/x-riff',
            'text/plain'
        ];

        if (
            in_array($mime, $genericBinaryMimes, true) &&
            in_array($extension, $binaryFallbackAllowed, true)
        ) {
            return true;
        }

        return false;
    }

    public function delete(ProductFile $file)
    {
        FileManager::deleteDigitalFile($file->product_id, $file->stored_name);
        $file->delete();
    }
}
