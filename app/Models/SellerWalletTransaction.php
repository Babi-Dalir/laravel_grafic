<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerWalletTransaction extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'type',
        'description',
        'balance_after',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
