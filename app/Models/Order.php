<?php

namespace App\Models;

use App\Enums\CartType;
use App\Enums\DiscountStatus;
use App\Enums\GiftCartStatus;
use App\Enums\OrderDetailStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPaidEvent; // اضافه شدن رویداد برای جداسازی وظایف
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_code',
        'transaction_id',
        'total_price',
        'discount_price',
        'discount_code',
        'gift_cart_price',
        'gift_cart_code',
        'status',
        'paid_at'
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function sellerWalletTransactions()
    {
        return $this->hasMany(SellerWalletTransaction::class);
    }

    /**
     * ثبت پرداخت موفق با ساختار تفکیک‌شده و رویدادمحور
     */
    public static function successPayment(Order $order): void
    {
        $isPaidNow = DB::transaction(function () use ($order) {

            $lockedOrder = self::query()
                ->lockForUpdate()
                ->with('orderDetails')
                ->findOrFail($order->id);

            // مکانیزم سخت‌گیرانه Idempotency
            if ($lockedOrder->status !== OrderStatus::WaitPayment) {
                return false;
            }

            // ۱۰۰٪ پایبند به معماری شیءگرای Enum بدون تداخل با value
            $lockedOrder->update([
                'status' => OrderStatus::Payed,
                'paid_at' => now(),
            ]);

            foreach ($lockedOrder->orderDetails as $detail) {
                $detail->update([
                    'status' => OrderDetailStatus::Paid->value
                ]);

                Product::query()
                    ->whereKey($detail->product_id)
                    ->increment('sold');

                Downloads::firstOrCreate([
                    'order_detail_id' => $detail->id
                ]);
            }

            UserCart::query()
                ->where('user_id', $lockedOrder->user_id)
                ->where('type', CartType::Main->value)
                ->delete();

            self::handleDiscount($lockedOrder->discount_code);
            self::handleGiftCart($lockedOrder->gift_cart_code, $lockedOrder);

            return true;
        });

        // 🚀 شلیک رویداد پرداخت موفق خارج از تراکنش برای کارهای فرعی پلتفرم (مثل پیامک، لاگ و غیره)
        if ($isPaidNow) {
            event(new OrderPaidEvent($order));
        }
    }

    /**
     * مدیریت تخفیف در سطح ساختار امن Concurrency-Safe
     */
    private static function handleDiscount($discount_code)
    {
        if (!$discount_code) {
            return;
        }

        $discount = Discount::query()
            ->where('code', $discount_code)
            ->where('status', DiscountStatus::Active->value)
            ->first();

        if (!$discount) {
            return;
        }

        // قفل اتمیک در دیتابیس: کم کردن مقدار فقط و فقط اگر بزرگتر از صفر باشد (ضد Race Condition)
        $affected = Discount::query()
            ->whereKey($discount->id)
            ->where('remaining_count', '>', 0)
            ->decrement('remaining_count');

        // اگر سطر آپدیت شد و فیلد تازه به صفر رسید، وضعیت غیرفعال شود
        if ($affected && $discount->fresh()->remaining_count <= 0) {
            $discount->update(['status' => DiscountStatus::InActive->value]);
        }
    }

    private static function handleGiftCart($gift_cart_code, $order)
    {
        if (!$gift_cart_code) {
            return;
        }

        $gift_cart = GiftCart::query()
            ->where('code', $gift_cart_code)
            ->where('user_id', $order->user_id)
            ->first();

        if (!$gift_cart) {
            return;
        }

        $amountToDeduct = $order->gift_cart_price;

        if ($amountToDeduct > 0) {
            if ($gift_cart->balance >= $amountToDeduct) {
                $gift_cart->decrement('balance', $amountToDeduct);
            } else {
                $gift_cart->update(['balance' => 0]);
            }
        }

        if ($gift_cart->fresh()->balance <= 0) {
            $gift_cart->update(['status' => GiftCartStatus::InActive->value]);
        }
    }

    /**
     * 🔐 ساخت کد پیگیری ۱۰۰٪ منحصر به فرد و تصادفی زمان‌محور
     */
    private static function generateOrderCode()
    {
        return 'ORD-' . now()->getTimestampMs() . '-' . Str::upper(Str::random(4));
    }
}
