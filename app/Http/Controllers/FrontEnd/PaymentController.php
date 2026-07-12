<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Downloads;
use App\Models\GiftCart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\SellerWalletTransaction;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class PaymentController extends Controller
{
    public function payment()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $shop_data = Session::get('shop_data');
        if (!$shop_data) {
            return redirect()->route('user.cart')->with('error', 'اطلاعات سفارش یافت نشد');
        }

        $carts = UserCart::getUserCart($user);
        if ($carts->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'سبد خرید شما خالی است');
        }

        $total_price = 0;
        $product_discount_price = 0;

        foreach ($carts as $cart) {
            $product = $cart->product;
            if (!$product || $product->status !== ProductStatus::Approved->value) {
                continue;
            }
            $total_price += $product->final_price;
            $product_discount_price += ($product->main_price - $product->final_price);
        }

        $initial_subtotal = $total_price;
        $discount_code_price = 0;
        $gift_cart_code_price = 0;

        // 🟢 شروع تراکنش برای ساخت اتمیک سفارش، رزرو کوپن و قفل کارت هدیه
        DB::beginTransaction();
        try {
            // ایجاد سفارش اولیه با وضعیت در انتظار پرداخت
            $order = Order::query()->create([
                'user_id'         => $user->id,
                'order_code'      => 'ORD-' . now()->getTimestampMs() . '-' . Str::upper(Str::random(4)),
                'total_price'     => $total_price, // مقدار موقت تا پیش از محاسبه تخفیف‌ها
                'discount_price'  => $product_discount_price,
                'discount_code'   => $shop_data['discount_code'] ?? null,
                'gift_cart_price' => 0,
                'gift_cart_code'  => $shop_data['gift_cart_code'] ?? null,
                'status'          => OrderStatus::WaitPayment->value,
            ]);

            // ۱. محاسبات و رزرو تخفیف (بدون کاهش ردیف از جدول اصلی، ثبت در جدول میانی با حضور order_id)
            if (!empty($shop_data['discount_code'])) {
                $result = Discount::calculateDiscount(
                    $shop_data,
                    $total_price,
                    $discount_code_price,
                    $order->id,
                    $user->id
                );
                $total_price = $result['total_price'];
                $discount_code_price = $result['discount_code_price'];
            }
            $total_discount_price = $product_discount_price + $discount_code_price;

            // ۲. محاسبات و قفل اتمیک کارت هدیه (داخل تراکنش با استفاده از LockForUpdate)
            if (!empty($shop_data['gift_cart_code'])) {
                $result = GiftCart::calculateGiftCart(
                    $shop_data,
                    $total_price,
                    $gift_cart_code_price,
                    $user->id
                );
                $total_price = $result['total_price'];
                $gift_cart_code_price = $result['gift_cart_code_price'];
            }

            $total_price = max(0, $total_price);

            // به‌روزرسانی نهایی مقادیر سفارش پس از اعمال تخفیف‌ها و کارت‌های هدیه
            $order->update([
                'total_price'     => $total_price,
                'discount_price'  => $total_discount_price,
                'gift_cart_price' => $gift_cart_code_price,
            ]);

            // 🟢 پچ تقسیم کوپن: فیلتر کردن سبد خرید فقط برای آیتم‌های دارای ارزش پولی
            $paidCarts = $carts->filter(fn($c) => $c->product && $c->product->final_price > 0);
            $paid_carts_count = $paidCarts->count();
            $processed_paid_carts = 0;
            $remaining_coupon_pool = $discount_code_price;

            foreach ($carts as $cart) {
                $product = Product::query()->where('id', $cart->product_id)->first();
                if (!$product) {
                    throw new \Exception('محصول یافت نشد یا غیرفعال شده است');
                }

                $allocated_coupon_discount = 0;

                if ($product->final_price > 0 && $remaining_coupon_pool > 0) {
                    $processed_paid_carts++;
                    if ($processed_paid_carts === $paid_carts_count) {
                        // آیتم پولی آخر کل استخر باقیمانده را برمی‌دارد (حتی اگر محصولات رایگان بعد از آن بیایند)
                        $allocated_coupon_discount = $remaining_coupon_pool;
                    } else {
                        $allocated_coupon_discount = round(($product->final_price / $initial_subtotal) * $discount_code_price);
                        $allocated_coupon_discount = min($allocated_coupon_discount, $product->final_price, $remaining_coupon_pool);
                    }
                    $remaining_coupon_pool -= $allocated_coupon_discount;
                }

                OrderDetail::createOrderDetail($order, $cart, $product, $allocated_coupon_discount);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Initialization Failed: ' . $e->getMessage());
            return redirect()->route('user.cart')->with('error', 'خطا در ایجاد سفارش: ' . $e->getMessage());
        }

        // ۳. سناریوی فاکتور رایگان یا آفلاین
        if ($total_price <= 0 || (isset($shop_data['payment_type']) && $shop_data['payment_type'] === 'offline')) {
            try {
                Order::successPayment($order, 'OFFLINE_OR_FREE');
                Session::forget('shop_data');
                $result = 'success';
            } catch (\Exception $e) {
                Log::error('Free/Offline Payment Execution Error: ' . $e->getMessage());
                $result = 'failed';
            }

            $downloads = $this->getDownloads($order);
            return view('frontend.shopping_result', compact('order', 'result', 'downloads'));
        }

        // ۴. سناریوی پرداخت آنلاین عادی
        try {
            $paymentType = $shop_data['payment_type'] ?? 'zarinpal';
            Session::put("order_gateway_{$order->id}", $paymentType);

            return Payment::via($paymentType)
                ->purchase(
                    (new Invoice)->amount((int)$total_price),
                    function ($driver, $transactionId) use ($order) {
                        $order->update([
                            'transaction_id' => $transactionId
                        ]);
                    }
                )
                ->pay()
                ->render();
        } catch (\Exception $e) {
            Log::error('Payment Gateway Error: ' . $e->getMessage());
            // آزادسازی کدهای رزرو شده به دلیل ناموفق بودن اتصال به درگاه
            Order::releaseReservations($order);
            return redirect()->route('user.cart')->with('error', 'اتصال به درگاه پرداخت با خطا مواجه شد.');
        }
    }

    public function callback(Request $request)
    {
        $authority = $request->Authority;
        $order = Order::query()->where('transaction_id', $authority)->first();
        $downloads = collect();

        if (!$order) {
            return view('frontend.shopping_result', ['result' => 'failed', 'order' => null, 'downloads' => $downloads]);
        }

        if ($order->status === OrderStatus::Payed) {
            $downloads = $this->getDownloads($order);
            return view('frontend.shopping_result', ['order' => $order, 'result' => 'success', 'downloads' => $downloads]);
        }

        if ($request->Status !== 'OK') {
            Order::releaseReservations($order);
            return view('frontend.shopping_result', ['order' => $order, 'result' => 'failed', 'downloads' => $downloads]);
        }

        try {
            $gateway = Session::get("order_gateway_{$order->id}", 'zarinpal');
            $payment = Payment::via($gateway)
                ->amount((int) $order->total_price)
                ->transactionId($authority)
                ->verify();

            $refId = $payment->getReferenceId() ?? $authority;

            // 🚀 موفقیت واقعی: کل فرآیند اتمیک لجر، ولت و دانلود در متد داخلی زیر است
            Order::successPayment($order, $refId);

            Session::forget("order_gateway_{$order->id}");
            Session::forget('shop_data');
            $result = 'success';
            $downloads = $this->getDownloads($order);
        } catch (\Exception $e) {
            Log::error('Payment Verification Error: ' . $e->getMessage());
            Order::releaseReservations($order);
            $result = 'failed';
        }

        return view('frontend.shopping_result', compact('order', 'result', 'downloads'));
    }

    private function getDownloads($order)
    {
        return Downloads::query()
            ->where('user_id', $order->user_id)
            ->whereHas('orderDetail', function ($q) use ($order) {
                $q->where('order_id', $order->id);
            })
            ->with('product')
            ->get();
    }
}
