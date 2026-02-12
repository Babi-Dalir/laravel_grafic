<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'name',
        'code'

    ];
    public function products()
    {
        return $this->belongsToMany(Product::class, 'color_product');
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }
    public static function createColor($request)
    {
        Color::query()->create([
            'name'=>$request->input('name'),
            'code'=>$request->input('code'),
        ]);
    }
    public static function updateColor($request,$id)
    {
        $color = Color::query()->find($id);
        $color->update([
            'name'=>$request->input('name'),
            'code'=>$request->input('code'),
        ]);
    }
}
