<?php

namespace App\Models;

use App\Enums\SettlementStatus;
use Illuminate\Database\Eloquent\Model;

class SellerSettlement extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'status',
        'reference_id',
        'admin_note',
        'paid_at',
        'paid_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function transactions()
    {
        return $this->hasMany(SellerWalletTransaction::class, 'settlement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function isPending(): bool
    {
        return $this->status === SettlementStatus::Pending->value;
    }

    public function isPaid(): bool
    {
        return $this->status === SettlementStatus::Paid->value;
    }
}
