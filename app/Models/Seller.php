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
        'status',
        'bank_verified',
        'verified_at'
    ];

    protected $casts = [
        'bank_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(SellerWalletTransaction::class);
    }

    /**
     * 🧠 REAL BALANCE (settled money)
     */
    public function getBalanceAttribute()
    {
        return $this->transactions()
            ->where('status', 'settled')
            ->sum('amount');
    }

    /**
     * 🧠 PENDING (30 days lock)
     */
    public function getPendingBalanceAttribute()
    {
        return $this->transactions()
            ->where('status', 'pending')
            ->sum('amount');
    }
}
