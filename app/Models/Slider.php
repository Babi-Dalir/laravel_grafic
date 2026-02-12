<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Slider extends Model
{
    protected $fillable = [
        'image',
        'link'
    ];
    public static function createSlider($request)
    {
        Cache::forget('sliders');
        Slider::query()->create([
            'link'=>$request->input('link'),
            'image'=>ImageManager::saveImage('sliders',$request->image)
        ]);
    }
    public static function updateSlider($request,$id)
    {
        Cache::forget('sliders');
        $slider = Slider::query()->find($id);
        $slider->update([
            'link'=>$request->input('link'),
            'image'=>$request->image ? ImageManager::saveImage('sliders',$request->image) : $slider->image
        ]);
    }
}
