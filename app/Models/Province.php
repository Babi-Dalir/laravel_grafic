<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable=[
        'province'
    ];
    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public static function createProvince($request)
    {
        Province::query()->create([
            'province'=>$request->input('province'),
        ]);
    }
    public static function updateProvince($request,$id)
    {
        $province = Province::query()->find($id);
        $province->update([
            'province'=>$request->input('province'),
        ]);
    }
}
