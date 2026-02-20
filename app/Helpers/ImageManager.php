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
    public static function saveProductImage($table, $image)
    {
        if ($image) {
            $name = $image->hashName();
            $manager = new IM(new \Intervention\Image\Drivers\Imagick\Driver());

            try {
                $img = $manager->read($image->getRealPath());

                // ۱. Thumbnail (برای لیست‌های کوچک در موبایل و دسکتاپ)
                $smallImage = clone $img;
                $smallImage->cover(300, 300); // کمی بزرگتر از قبل برای کیفیت بهتر در نمایشگرهای Retina
                Storage::disk('public')->put($table.'/small/'.$name, (string)$smallImage->toJpeg(75));

                // ۲. تصویر اصلی (مناسب برای زوم و نمایش در همه دستگاه‌ها)
                $bigImage = clone $img;

                // عرض ۱۲۰۰ پیکسل استاندارد طلایی برای سال ۲۰۲۶ است
                $bigImage->scale(width: 1200);

                // اعمال واترمارک
                $watermarkPath = public_path('panel/assets/media/image/watermark.png');
                if (file_exists($watermarkPath)) {
                    $watermark = $manager->read($watermarkPath);
                    // واترمارک را متناسب با عرض ۱۲۰۰ تنظیم کن
                    $watermark->scale(width: 500);
                    $bigImage->place($watermark, 'center', 0, 0, 25);
                }

                // ذخیره با کیفیت ۸۰ درصد (تعادل عالی بین کیفیت و حجم برای موبایل)
                Storage::disk('public')->put($table.'/big/'.$name, (string)$bigImage->toJpeg(80));

                return $name;
            } catch (\Exception $e) {
                \Log::error("Responsive Image Error: " . $e->getMessage());
                Storage::disk('public')->put($table.'/big/'.$name, file_get_contents($image));
                return $name;
            }
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
    public static function ckeditorImage($table, $image)
    {
        if ($image) {
            $name = $image->hashName();
            // استفاده از درایور Imagick برای رندرینگ با کیفیت
            $manager = new IM(new \Intervention\Image\Drivers\Imagick\Driver());

            try {
                $img = $manager->read($image->getRealPath());

                // عرض ۱۲۰۰ پیکسل: نقطه تعادل برای دسکتاپ و تبلت‌های عریض
                // اگر عکس اصلی کوچکتر از ۱۲۰۰ باشد، بزرگش نمی‌کند (حفظ کیفیت)
                $img->scale(width: 1200);

                // استفاده از کیفیت ۷۵٪ به جای ۸۵٪:
                // این ۱۰ درصد کاهش، حجم فایل را تا ۴۰٪ کمتر می‌کند بدون افت کیفیت محسوس
                // که برای کاربر موبایل فوق‌العاده است.
                Storage::disk('public')->put($table.'/big/'.$name, (string)$img->toJpeg(75));

                // استفاده از آدرس کامل با asset
                return asset("storage/$table/big/".$name);

            } catch (\Exception $e) {
                // اگر در پردازش تصویر مشکلی بود، فایل اصلی را بدون دستکاری ذخیره کن
                \Log::error("CKEditor Image Upload Error: " . $e->getMessage());
                Storage::disk('public')->put($table.'/big/'.$name, file_get_contents($image));
                return asset("storage/$table/big/".$name);
            }
        }
    }
}
