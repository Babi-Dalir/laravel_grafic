<?php

namespace App\Models;

use App\Enums\CartType;
use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'count',
        'type',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productPrice($product_id,$color_id,$guaranty_id)
    {
        $product_price = ProductPrice::query()
            ->where('product_id',$product_id)
            ->first();
        if ($product_price){
            return $product_price->price;
        }
    }

    public static function getUserCart($user)
    {
        return UserCart::query()
            ->where('user_id', $user->id)
            ->where('type', CartType::Main->value)->get();
    }
}
