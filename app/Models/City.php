<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'city',
        'province_id',
        'send_time',
        'send_price',

    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public static function createCity($request)
    {
        City::query()->create([
            'city'=>$request->input('city'),
            'province_id'=>$request->input('province_id'),
            'send_time'=>$request->input('send_time'),
            'send_price'=>$request->input('send_price'),
        ]);
    }
    public static function updateCity($request,$id)
    {
        $city = City::query()->find($id);
        $city->update([
            'city'=>$request->input('city'),
            'province_id'=>$request->input('province_id'),
            'send_time'=>$request->input('send_time'),
            'send_price'=>$request->input('send_price'),
        ]);
    }
}
