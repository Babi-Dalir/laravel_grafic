<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Review extends Model
{
    protected $fillable = [
        'name',
        'product_id',
        'description',

    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public static function createReview($request,$product_id)
    {
        Review::query()->create([
            'name'=>$request->input('name'),
            'product_id'=>$product_id,
            'description'=>$request->input('description'),

        ]);
    }
    public static function updateReview($request,$id,$product_id)
    {
        $review = Review::query()->find($id);
        $review->update([
            'name'=>$request->input('name'),
            'product_id'=>$product_id,
            'description'=>$request->input('description'),
        ]);
    }
}
