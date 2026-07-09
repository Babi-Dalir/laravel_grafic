<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Enums\WalletTransactionStatus;
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
    /**
     * 🧠 REGISTER SALE (SAFE LEDGER ENTRY) - EXCLUSIVELY FOR DIGITAL PLATFORM
     */
    public static function registerSale($orderDetails): void
    {
        DB::transaction(function () use ($orderDetails) {

            foreach ($orderDetails as $detail) {

                $product = $detail->product;
                if (!$product) continue;

                // 🟢 واکشی زنده و امن فروشنده بر اساس مدل Seller
                $sellerId = null;
                if ($product->seller) {
                    $sellerId = $product->seller->id;
                } elseif ($product->user_id) {
                    $sellerId = Seller::query()->where('user_id', $product->user_id)->value('id');
                }

                if (!$sellerId) continue;

                // استفاده از شناسه یونیک آیتم سفارش
                $referenceId = "order_detail_{$detail->id}";

                // گارد بررسی لایه اپلیکیشن (چک اول)
                $exists = self::where('reference_id', $referenceId)->exists();
                if ($exists) continue;

                // 🟢 اختصاصی فروشگاه دیجیتال: آزادسازی آنی و بدون تاخیر (Instant Release)
                // چون محصول فایل است و همان لحظه دانلود شده، پول فورا در دیتابیس آزاد و آماده تسویه است.
                self::create([
                    'seller_id'    => $sellerId,
                    'order_id'     => $detail->order_id,
                    'amount'       => $detail->seller_share, // واریز سهم خالص فروشنده
                    'type'         => TransactionType::Sale->value,
                    'description'  => "فروش محصول دیجیتال «{$product->name}» بابت آیتم فاکتور #{$detail->id}",
                    'status'       => WalletTransactionStatus::Pending->value,
                    'release_at'   => now(), // 🚀 آنی و بدون بلوکه شدن ۷ یا ۳۰ روزه
                    'reference_id' => $referenceId,
                ]);
            }
        });
    }
}
