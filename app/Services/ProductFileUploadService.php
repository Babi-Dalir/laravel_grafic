<?php

namespace App\Services;

use App\Models\ProductFile;
use Exception;
use Illuminate\Support\Facades\Storage;

class ProductFileUploadService
{
    /**
     * 🗑️ متد حذف فیزیکی فایل از استوریج دیجیتال (بر اساس تنظیمات دیسک شما)
     */
    public function delete(ProductFile $file): void
    {
        $finalPath = "products/{$file->product_id}/{$file->stored_name}";

        // دیسک دیجیتال شما طبق کدهای موجود 'digital_files' است
        $disk = 'digital_files';

        if (Storage::disk($disk)->exists($finalPath)) {
            Storage::disk($disk)->delete($finalPath);
        }

        $file->delete();
    }

    /**
     * بررسی انطباق مایم‌تایپ واقعی استخراج‌شده با پسوند ادعایی فایل
     */
    public function isValidMimeForExtension(string $extension, string $mime): bool
    {
        $extension = strtolower(trim($extension));
        $mime = strtolower(trim($mime));

        $corelMimes = [
            'application/vnd.corel-draw',
            'application/x-vnd.corel.draw.document+zip',
            'application/x-vnd.corel.zcf.draw.document+zip',
            'application/cdr',
            'application/coreldraw',
            'image/cdr',
            'application/x-cdr',
            'application/vnd.coreldraw',
            'application/x-coreldraw',
            'zz-application/zz-winassoc-cdr',
            'application/x-riff',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
            'image/x-cmx',
            'application/x-cmx',
            'image/x-cpt',
            'application/x-cpt',
            'application/x-cdt',
            'audio/x-riff'
        ];

        $corelExtensions = ['cdr', 'cdt', 'cmx', 'cpt'];

        if (in_array($extension, $corelExtensions, true)) {
            $corelKeywords = ['corel', 'coreldraw', 'zcf', 'vnd.corel', 'draw'];
            foreach ($corelKeywords as $keyword) {
                if (str_contains($mime, $keyword)) {
                    return true;
                }
            }
        }

        $validMimes = [
            'jpg'  => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png'  => ['image/png', 'image/x-png'],
            'webp' => ['image/webp', 'image/x-webp'],
            'tiff' => ['image/tiff', 'image/x-tiff'],
            'svg'  => ['image/svg+xml', 'application/xml', 'text/xml'],
            'pdf'  => ['application/pdf', 'application/x-pdf'],
            'zip'  => [
                'application/zip',
                'application/x-zip-compressed',
                'application/x-zip',
                'multipart/x-zip',
                'application/x-compressed'
            ],
            'psd'  => ['image/vnd.adobe.photoshop', 'application/x-photoshop', 'image/psd', 'application/photoshop'],
            'ai'   => [
                'application/postscript',
                'application/vnd.adobe.illustrator',
                'application/pdf',
                'application/x-pdf',
                'application/illustrator'
            ],
            'eps'  => [
                'application/postscript',
                'image/x-eps',
                'image/eps',
                'application/eps',
                'application/x-eps'
            ],
            'cdr'  => $corelMimes,
            'cdt'  => $corelMimes,
            'cmx'  => $corelMimes,
            'cpt'  => $corelMimes,
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

        if (in_array($mime, $validMimes[$extension], true)) {
            return true;
        }

        $binaryFallbackAllowed = array_merge(['ai', 'psd', 'eps', 'obj', 'stl', '3ds', 'stp', 'step', 'dxf'], $corelExtensions);

        $genericBinaryMimes = [
            'application/octet-stream',
            'application/x-riff',
            'audio/x-riff',
            'application/zip',
            'application/x-zip-compressed',
            'application/x-zip',
            'text/plain'
        ];

        if (
            in_array($extension, $binaryFallbackAllowed, true) &&
            (in_array($mime, $genericBinaryMimes, true) || str_contains($mime, 'zip'))
        ) {
            return true;
        }

        return false;
    }
}
