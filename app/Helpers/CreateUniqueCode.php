<?php

namespace App\Helpers;
use Illuminate\Support\Str;

class CreateUniqueCode
{
    public static function generateRandomString($length = 6, $model)
    {
        // ۱. تولید رشته تصادفی با استفاده از متد داخلی لاراول (امن و سریع)
        $randomString = Str::random($length);

        // ۲. بررسی وجود کد در دیتابیس با متد بهینه exists
        $codeExist = $model::query()->where('code', $randomString)->exists();

        if ($codeExist) {
            // ۳. فراخوانی مجدد با حفظ طول اولیه
            return self::generateRandomString($length, $model);
        }

        return $randomString;
    }

    public static function generateRandomInteger($length = 6,$model)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $codeExist = $model::query()->where('code', $randomString)->first();
        if ($codeExist) {
            return self::generateRandomInteger(6,$model);
        } else {
            return $randomString;
        }
    }
}
