<?php

namespace App\Models;

use App\Enums\CartType;
use App\Enums\DiscountStatus;
use App\Enums\DiscountUsageStatus;
use App\Enums\DownloadStatus;
use App\Enums\GiftCartStatus;
use App\Enums\OrderDetailStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPaidEvent;
use App\Services\FinancialLedgerService;

// 🟢 لود سرویس مالی جدید
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'transaction_id',
        'gateway_token',
        'payment_reference',
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
     * ثبت پرداخت موفق اتمیک با تفکیک لایه حسابداری
     */
    public static function successPayment(Order $order, string $paymentReference): void
    {
        $lockedOrder = DB::transaction(function () use ($order, $paymentReference) {

            $currentOrder = self::query()
                ->lockForUpdate()
                ->with(['orderDetails' => fn($q) => $q->withTrashed()
                    ->with(['product' => fn($p) => $p->withTrashed()
                        ->with('seller')])])
                ->findOrFail($order->id);

            if ($currentOrder->status === OrderStatus::Payed->value) {
                return $currentOrder;
            }

            // ۱. تغییر وضعیت پایه سفارش
            $currentOrder->update([
                'status' => OrderStatus::Payed->value,
                'payment_reference' => $paymentReference,
                'paid_at' => now(),
            ]);

            // ۲. 🚀 واگذاری تمام کارهای ریز حسابداری به سرویس مالی اختصاصی (خلوت شدن چشمگیر کد)
            FinancialLedgerService::recordOrderMetrics($currentOrder, $paymentReference);

            // ۳. فرآیند توزیع فایل دانلود و آپدیت وضعیت آیتم‌ها
            foreach ($currentOrder->orderDetails as $detail) {
                $detail->update([
                    'status' => OrderDetailStatus::Paid->value
                ]);

                $product = $detail->product;
                if ($product) {
                    Product::query()->whereKey($product->id)->increment('sold');
                }

                Downloads::firstOrCreate([
                    'order_detail_id' => $detail->id
                ], [
                    'user_id' => $currentOrder->user_id,
                    'product_id' => $detail->product_id,
                    'token' => Str::random(100),
                    'max_download' => 5,
                    'expire_at' => now()->addYear(),
                    'status' => DownloadStatus::Active->value
                ]);
            }

            // ۴. واریز سهم ولت فروشندگان در کیف پول
            SellerWalletTransaction::registerSale($currentOrder->orderDetails);

            // ۵. رفتارهای نهایی کوپن و کارت هدیه
            $discountUsage = DB::table('discount_usages')
                ->where('order_id', $currentOrder->id)
                ->where('status', DiscountUsageStatus::Reserved->value)
                ->first();

            if ($discountUsage) {
                DB::table('discount_usages')
                    ->where('id', $discountUsage->id)
                    ->update([
                        'status' => DiscountUsageStatus::Used->value
                    ]);

                $affected = Discount::query()
                    ->where('id', $discountUsage->discount_id)
                    ->where('remaining_count', '>', 0)
                    ->decrement('remaining_count');
                if ($affected) {
                    $freshDiscount = Discount::query()->find($discountUsage->discount_id);
                    if ($freshDiscount && $freshDiscount->remaining_count <= 0) {
                        $freshDiscount->update(['status' => DiscountStatus::InActive->value]);
                    }
                }
            }

            UserCart::query()
                ->where('user_id', $currentOrder->user_id)
                ->where('type', CartType::Main->value)
                ->delete();

            if ($currentOrder->gift_cart_code) {
                self::confirmGiftCart($currentOrder);
            }

            return $currentOrder;
        });

        if ($lockedOrder && $lockedOrder->wasChanged('status')) {
            event(new OrderPaidEvent($lockedOrder->fresh()));
        }
    }

    private static function confirmGiftCart($order)
    {
        $gift_cart = GiftCart::query()
            ->where('code', $order->gift_cart_code)
            ->where('user_id', $order->user_id)
            ->lockForUpdate()
            ->first();
        if ($gift_cart && $order->gift_cart_price > 0) {
            $gift_cart->decrement('balance', $order->gift_cart_price);
            if ($gift_cart->fresh()->balance <= 0) {
                $gift_cart->update([
                    'status' => GiftCartStatus::InActive->value
                ]);
            }
        }
    }

    public static function releaseReservations(Order $order): void
    {
        DB::transaction(function () use ($order) {
            DB::table('discount_usages')
                ->where('order_id', $order->id)
                ->where('status', DiscountUsageStatus::Reserved->value)
                ->update([
                    'status' => DiscountUsageStatus::Cancelled->value
                ]);
            $order->update([
                'status' => OrderStatus::Cancelled->value
            ]);
        });
    }
}
