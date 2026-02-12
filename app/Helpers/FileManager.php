<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as IM;

class FileManager
{
    public static function saveContract($file,$company)
    {
        if ($file){
            $name = $file->hashName();
            Storage::disk('files')->put('/contract/'.$company,$file);
            return $name;
        }else{
            return "";
        }
    }
}
