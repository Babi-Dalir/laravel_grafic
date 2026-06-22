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

            'zip'  => ['application/zip', 'application/x-zip-compressed'],

            'psd'  => ['image/vnd.adobe.photoshop', 'application/x-photoshop'],
            'ai'   => ['application/postscript', 'application/vnd.adobe.illustrator'],
            'eps'  => ['application/postscript', 'image/x-eps'],

            'cdr'  => [
                'application/cdr',
                'application/coreldraw',
                'image/cdr',
                'application/x-cdr',
                'application/vnd.coreldraw',
                'application/x-coreldraw',
                'zz-application/zz-winassoc-cdr',
                'application/x-riff', // مهم برای لینوکس / فایل‌های واقعی Corel
            ],

            'art'  => ['image/x-jg'],

            'dxf'  => ['image/vnd.dxf', 'image/x-dxf', 'application/dxf', 'text/plain'],

            'stl'  => ['application/sla', 'application/stl', 'text/plain'],

            'obj'  => ['text/plain', 'application/object'],

            '3ds'  => ['image/x-3ds', 'application/x-3ds'],

            'stp'  => ['application/step', 'text/plain'],
            'step' => ['application/step', 'text/plain'],

            'ttf'  => ['font/ttf', 'font/sfnt', 'application/x-font-ttf'],
            'otf'  => ['font/otf', 'font/sfnt', 'application/x-font-opentype'],
        ];

        if (!isset($validMimes[$extension])) {
            return false;
        }

        // 1. تطابق دقیق (اصلی و امن)
        if (in_array($mime, $validMimes[$extension], true)) {
            return true;
        }

        /**
         * 2. fallback محدود فقط برای فایل‌های مهندسی/گرافیکی سنگین
         * (نه تصاویر، نه zip، نه pdf)
         */
        $binaryFallbackAllowed = [
            'cdr', 'ai', 'psd', 'eps', 'obj', 'stl', '3ds', 'stp', 'step'
        ];

        $genericBinaryMimes = [
            'application/octet-stream',
            'application/x-riff'
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
