<?php

namespace App\Models;

use App\Enums\CartType;
use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'type',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public static function getUserCart($user)
    {
        return UserCart::query()
            ->with('product')
            ->where('user_id', $user->id)
            ->where('type', CartType::Main->value)
            ->get();
    }
}
