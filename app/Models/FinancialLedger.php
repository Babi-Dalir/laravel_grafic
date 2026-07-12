<?php

namespace App\Models;

use App\Enums\FinancialEntryType;
use App\Enums\FinancialLedgerType;
use Illuminate\Database\Eloquent\Model;

class FinancialLedger extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'entry_type',
        'amount',
        'description'
    ];

    protected function casts(): array
    {
        return [
            'type' => FinancialLedgerType::class,
            'entry_type' => FinancialEntryType::class,
            'amount' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
