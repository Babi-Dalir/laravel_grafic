<?php

namespace App\Models;

use App\Enums\DiscountUsageStatus;
use Illuminate\Database\Eloquent\Model;

class DiscountUsage extends Model
{
    protected $fillable = [
        'discount_id',
        'user_id',
        'order_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => DiscountUsageStatus::class,
        ];
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
