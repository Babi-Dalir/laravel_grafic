<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as IM;

class FileManager
{
    public static function saveContract($file, $company)
    {
        if ($file) {
            $name = $file->hashName();
            Storage::disk('files')->put('/contract/' . $company, $file);
            return $name;
        } else {
            return "";
        }
    }

    public static function saveDigitalFile($file, $productSlug)
    {
        if ($file) {
            // نام فایل امن
            $name = $file->hashName();

            // مسیر ذخیره سازی
            $path = 'private_files/products/' . $productSlug;

            // ذخیره فایل در استوریج
            Storage::disk('local')->putFileAs($path, $file, $name);

            return $name;
        }

        return null;
    }

    public static function deleteDigitalFile($productSlug, $fileName)
    {
        if ($fileName) {
            $path = 'private_files/products/' . $productSlug . '/' . $fileName;

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }
    }
}
