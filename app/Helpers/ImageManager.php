<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as IM;

class ImageManager
{
    public static function saveImage($table,$image)
    {
        if ($image){
            $name = $image->hashName();
            $manager = new IM(Driver::class);
            $smallImage = $manager->read($image->getRealPath());
            $bigImage = $manager->read($image->getRealPath());
            $smallImage->resize(width: 256,height: 256);
            Storage::disk('public')->put($table.'/small/'.$name,(string)$smallImage->toPng());
            Storage::disk('public')->put($table.'/big/'.$name,(string)$bigImage->toPng());
            return $name;
        }else{
            return "";
        }
    }

    public static function unlinkImage($table, $object)
    {
        if ($object->image) {
            $smallPath = $table . '/small/' . $object->image;
            $bigPath = $table . '/big/' . $object->image;

            // چک کردن وجود فایل قبل از حذف برای جلوگیری از خطا
            if (Storage::disk('public')->exists($smallPath)) {
                Storage::disk('public')->delete($smallPath);
            }
            if (Storage::disk('public')->exists($bigPath)) {
                Storage::disk('public')->delete($bigPath);
            }
        }
    }
    public static function ckeditorImage($table,$image)
    {
            $name = $image->hashName();
            $manager = new IM(Driver::class);
            $bigImage = $manager->read($image->getRealPath());
            Storage::disk('public')->put($table.'/big/'.$name,(string)$bigImage->toPng());
            return url("images/$table/big/".$name);
    }
}
