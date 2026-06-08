<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'brand_name',
        'national_code',
        'first_name',
        'last_name',
        'card_number',
        'account_number',
        'iban',
        'balance',
        'pending_balance',
        'total_income',
        'total_settlement',
        'sales_count',
        'status',
        'bank_verified',
        'verified_at',
        'last_settlement_at'
    ];

    protected $casts = [
        'bank_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(SellerWalletTransaction::class);
    }

    public function settlementRequests()
    {
        return $this->hasMany(SellerSettlement::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
