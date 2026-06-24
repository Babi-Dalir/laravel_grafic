<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Banner extends Model
{
    protected $fillable = [
        'image',
        'type'
    ];

    /**
     * متد متمرکز برای فراموشی کش بنرها (DRY)
     */
    public static function clearCache(): void
    {
        Cache::forget('banners');
    }

    public static function createBanner($request)
    {
        self::clearCache();

        self::query()->create([
            'type'  => $request->input('type'),
            'image' => ImageManager::saveImage('banners', $request->image)
        ]);
    }

    public static function updateBanner($request, $id)
    {
        self::clearCache();

        $banner = Banner::findOrFail($id);
        $imageName = $banner->image; // مقدار پیش‌فرض را تصویر فعلی قرار می‌دهیم

        if ($request->hasFile('image')) {
            ImageManager::unlinkImage('banners', $banner);
            $imageName = ImageManager::saveImage('banners', $request->image);
        }

        $banner->update([
            'type'  => $request->input('type'),
            'image' => $imageName
        ]);
    }
}
