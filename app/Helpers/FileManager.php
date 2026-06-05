<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as IM;

class FileManager
{
    public static function saveResume($file,$user_id)
    {
        if (!$file) {
            return null;
        }

        return $file->store(
            "resume//{$user_id}",
            'files'
        );
    }

    public static function saveDigitalFile($file, $product_id)
    {
        if ($file) {
            // نام فایل امن
            $name = $file->hashName();

            // مسیر ذخیره سازی
            $path = 'products/' . $product_id;

            // ذخیره فایل در استوریج
            Storage::disk('digital_files')->putFileAs($path, $file, $name);

            return $name;
        }

        return null;
    }

    public static function deleteDigitalFile($product_id, $fileName)
    {
        if ($fileName) {
            $path = 'products/' . $product_id . '/' . $fileName;

            if (Storage::disk('digital_files')->exists($path)) {
                Storage::disk('digital_files')->delete($path);
            }
        }
    }
}
