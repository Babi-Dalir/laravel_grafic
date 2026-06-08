<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerSettlement extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'status',
        'admin_note',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
