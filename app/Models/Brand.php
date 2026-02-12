<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'image'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public static function createBrand($request)
    {
        Brand::query()->create([
            'name'=>$request->input('name'),
            'image'=>ImageManager::saveImage('brands',$request->image)
        ]);
    }
    public static function updateBrand($request,$id)
    {
        $brand = Brand::query()->find($id);
        $brand->update([
            'name'=>$request->input('name'),
            'image'=>$request->image ? ImageManager::saveImage('brands',$request->image) : $brand->image
        ]);
    }
}
