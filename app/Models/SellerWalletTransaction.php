<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SellerWalletTransaction extends Model
{
    protected $fillable = [
        'seller_id',
        'order_id',
        'amount',
        'type',
        'description',
        'status',
        'reference_id',
        'release_at',
        'settled_at',
        'settlement_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'release_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function settlement()
    {
        return $this->belongsTo(SellerSettlement::class, 'settlement_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 🧠 REGISTER SALE (SAFE LEDGER ENTRY)
     */
    public static function registerSale($orderDetails): void
    {
        DB::transaction(function () use ($orderDetails) {

            foreach ($orderDetails as $detail) {

                $product = $detail->product;
                if (!$product?->seller) continue;

                $referenceId = "order_{$detail->order_id}_product_{$product->id}";

                // 🛑 جلوگیری از duplicate
                $exists = self::where('reference_id', $referenceId)->exists();

                if ($exists) continue;

                self::create([
                    'seller_id' => $product->seller->id,
                    'order_id' => $detail->order_id,
                    'amount' => $detail->seller_share,
                    'type' => TransactionType::Sale->value,
                    'description' => "فروش محصول {$product->name}",
                    'status' => 'pending',
                    'release_at' => now()->addDays(30),

                    // 🧠 مهم‌ترین بخش
                    'reference_id' => $referenceId,
                ]);
            }
        });
    }
}
