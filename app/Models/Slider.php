<?php

namespace App\Models;

use App\Enums\SliderStatus;
use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Slider extends Model
{
    protected $fillable = [
        'image',
        'link',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'status' => SliderStatus::class,
        ];
    }

    /**
     * متد متمرکز پاکسازی کش اسلایدرها
     */
    public static function clearCache(): void
    {
        Cache::forget('sliders');
    }

    public static function createSlider($request)
    {
        self::clearCache();

        self::query()->create([
            'link'  => $request->input('link'),
            'image' => ImageManager::saveImage('sliders', $request->image)
        ]);
    }

    public static function updateSlider($request, $id)
    {
        self::clearCache();

        $slider = Slider::findOrFail($id);
        $imageName = $slider->image; // مقدار پیش‌فرض

        if ($request->hasFile('image')) {
            ImageManager::unlinkImage('sliders', $slider);
            $imageName = ImageManager::saveImage('sliders', $request->image);
        }

        $slider->update([
            'link'  => $request->input('link'),
            'image' => $imageName
        ]);
    }
}
