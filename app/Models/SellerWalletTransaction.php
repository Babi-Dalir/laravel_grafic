<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class SellerWalletTransaction extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'type',
        'description',
        'order_id',
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

    public static function registerSale($orderDetails): void
    {
        foreach ($orderDetails as $detail) {

            $product = $detail->product;

            $seller = Seller::query()
                ->where('user_id', $product->user_id)
                ->first();

            if (! $seller) {
                continue;
            }

            $amount = $detail->price;

            $seller->increment(
                'pending_balance',
                $amount
            );

            $seller->increment(
                'total_income',
                $amount
            );

            $seller->increment(
                'sales_count'
            );

            self::create([
                'seller_id' => $seller->id,
                'order_id' => $detail->order_id,
                'amount' => $amount,
                'type' => TransactionType::Sale->value,
                'description' => "فروش محصول {$product->name}",
                'balance_after' => $seller->fresh()->pending_balance,
            ]);
        }
    }
}
