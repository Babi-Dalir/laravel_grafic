<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guaranty extends Model
{
    protected $fillable=[
        'name'
    ];
    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }
    public static function createGuaranty($request)
    {
        Guaranty::query()->create([
            'name'=>$request->input('name'),
        ]);
    }
    public static function updateGuaranty($request,$id)
    {
        $guaranty = Guaranty::query()->find($id);
        $guaranty->update([
            'name'=>$request->input('name'),
        ]);
    }
}
