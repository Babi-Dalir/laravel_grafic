<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'category_id',
        'commission_percent',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public static function createCommission($request)
    {
        Commission::query()->create([
            'commission_percent'=>$request->input('commission_percent'),
            'category_id'=>$request->input('category_id'),
        ]);
    }

    public static function updateCommission($request,$id)
    {
        $commission = Commission::query()->find($id);
        $commission->update([
            'commission_percent'=>$request->input('commission_percent'),
            'category_id'=>$request->input('category_id'),
        ]);
    }
}
