<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Enums\WalletTransactionStatus;
use Illuminate\Database\Eloquent\Model;

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
     * ثبت تراکنش ولت فروشندگان (همخوان با امنیت تراکنش مادر پلتفرم دیجیتال)
     */
    public static function registerSale($orderDetails): void
    {
        // 🟢 نکته ایمنی: تراکنش درونی به دلیل اجرای مستقیم داخل تراکنش اصلی Order حذف شد تا هم‌راستا عمل کنند.
        foreach ($orderDetails as $detail) {

            // پچ طلایی: لود امن محصول حتی در صورت پاک شدن موقت یا دائم از پنل
            $product = Product::withTrashed()->find($detail->product_id);
            if (!$product) continue;

            $sellerId = null;
            if ($product->user_id) {
                $sellerId = Seller::query()->where('user_id', $product->user_id)->value('id');
            }

            if (!$sellerId) continue;

            $referenceId = "order_detail_{$detail->id}";

            // گارد بررسی دوگانه پایداری برای جلوگیری از رکوردهای کامپلکس تکراری
            $exists = self::where('reference_id', $referenceId)->exists();
            if ($exists) continue;

            self::create([
                'seller_id'    => $sellerId,
                'order_id'     => $detail->order_id,
                'amount'       => $detail->seller_share,
                'type'         => TransactionType::Sale->value,
                'description'  => "فروش محصول دیجیتال «{$product->name}» بابت آیتم فاکتور #{$detail->id}",
                'status'       => WalletTransactionStatus::Pending->value,
                'release_at'   => now()->addDays(30),
                'reference_id' => $referenceId,
            ]);
        }
    }
}
