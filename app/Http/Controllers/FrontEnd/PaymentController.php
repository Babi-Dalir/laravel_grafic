<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\OrderStatus;
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
            return redirect()->route('user.cart')
                ->with('error', 'اطلاعات سفارش یافت نشد');
        }

        $carts = UserCart::getUserCart($user);

        if ($carts->isEmpty()) {
            return redirect()->route('user.cart')
                ->with('error', 'سبد خرید شما خالی است');
        }

        $total_price = 0;
        $product_discount_price = 0;

        foreach ($carts as $cart) {
            $product = $cart->product;
            if (!$product) continue;

            $total_price += $product->final_price;
            $product_discount_price += ($product->main_price - $product->final_price);
        }

        $discount_code_price = 0;
        $gift_cart_code_price = 0;

        /*
        |--------------------------------------------------------------------------
        | Discount
        |--------------------------------------------------------------------------
        */
        if (!empty($shop_data['discount_code'])) {
            $result = Discount::calculateDiscount(
                $shop_data,
                $total_price,
                $discount_code_price
            );

            $total_price = $result['total_price'];
            $discount_code_price = $result['discount_code_price'];
        }
        $total_discount_price = $product_discount_price + $discount_code_price;

        /*
        |--------------------------------------------------------------------------
        | Gift Cart
        |--------------------------------------------------------------------------
        */
        if (!empty($shop_data['gift_cart_code'])) {
            $result = GiftCart::calculateGiftCart(
                $shop_data,
                $total_price,
                $gift_cart_code_price
            );

            $total_price = $result['total_price'];
            $gift_cart_code_price = $result['gif_cart_code_price'];
        }

        DB::beginTransaction();

        try {
            // 🧠 اصلاح شد: ساخت مستقیم سفارش بر اساس فیلدهای Fillable مدل Order
            $order = Order::query()->create([
                'user_id'         => $user->id,
                'order_code'      => 'ORD-' . now()->getTimestampMs() . '-' . Str::upper(Str::random(4)),
                'total_price'     => $total_price,
                'discount_price'  => $total_discount_price,
                'discount_code'   => $shop_data['discount_code'] ?? null,
                'gift_cart_price' => $gift_cart_code_price,
                'gift_cart_code'  => $shop_data['gift_cart_code'] ?? null,
                'status'          => OrderStatus::WaitPayment->value, // وضعیت اولیه در انتظار پرداخت
            ]);

            $order_details = [];

            foreach ($carts as $cart) {
                $product = Product::query()
                    ->where('id', $cart->product_id)
                    ->first();

                if (!$product) {
                    throw new \Exception('محصول یافت نشد یا غیرفعال شده است');
                }

                $order_details[] = OrderDetail::createOrderDetail(
                    $order,
                    $cart,
                    $product
                );
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Creation Failed: ' . $e->getMessage());
            return redirect()->route('user.cart')->with('error', 'خطا در ایجاد سفارش: ' . $e->getMessage());
        }

        /*
        |--------------------------------------------------------------------------
        | Offline Payment (کیف پول یا تست آفلاین)
        |--------------------------------------------------------------------------
        */
        if (isset($shop_data['payment_type']) && $shop_data['payment_type'] === 'offline') {
            try {
                // 🧠 اصلاح شد: متد جدید مدل فقط خود شیء سفارش را می‌پذیرد
                Order::successPayment($order);
                SellerWalletTransaction::registerSale($order_details);

                $result = 'success';
            } catch (\Exception $e) {
                Log::error('Offline Payment Error: ' . $e->getMessage());
                $result = 'failed';
            }

            $downloads = Downloads::query()
                ->where('user_id', $order->user_id)
                ->whereHas('orderDetail', function ($q) use ($order) {
                    $q->where('order_id', $order->id);
                })
                ->with('product')
                ->get();

            return view('frontend.shopping_result', compact('order', 'result', 'downloads'));
        }

        /*
        |--------------------------------------------------------------------------
        | Online Payment (درگاه آنلاین زرین‌پال یا ...)
        |--------------------------------------------------------------------------
        */
        try {
            return Payment::via($shop_data['payment_type'])
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
            return redirect()->route('user.cart')->with('error', 'اتصال به درگاه پرداخت با خطا مواجه شد.');
        }
    }

   public function callback(Request $request)
{
    $authority = $request->Authority;

    // پیدا کردن سفارش بر اساس توکن درگاه
    $order = Order::query()
        ->where('transaction_id', $authority)
        ->first();

    // 🧼 راه حل ۲: تعریف پیش‌فرض متغیر دانلودها به صورت کالکشن خالی در بالای فانکشن
    $downloads = collect();

    if (!$order) {
        return view('frontend.shopping_result', [
            'result' => 'failed',
            'order' => null,
            'downloads' => $downloads // پاس دادن کالکشن خالی
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | مدیریت ایمن رفرش صفحه (اگر قبلاً پرداخت موفق بوده است)
    |--------------------------------------------------------------------------
    */
    if ($order->status === OrderStatus::Payed || $order->status === OrderStatus::Payed->value) {
        // اورراید کردن دانلودها با استفاده از متد کمکی
        $downloads = $this->getDownloads($order);

        return view('frontend.shopping_result', [
            'order' => $order,
            'result' => 'success',
            'downloads' => $downloads
        ]);
    }

    // اگر وضعیت برگشت از بانک OK نبود
    if ($request->Status !== 'OK') {
        return view('frontend.shopping_result', [
            'order' => $order,
            'result' => 'failed',
            'downloads' => $downloads // پاس دادن کالکشن خالی
        ]);
    }

    try {
        /*
        |--------------------------------------------------------------------------
        | تایید اصالت تراکنش (فقط برای بار اول)
        |--------------------------------------------------------------------------
        */
        $payment = Payment::via($order->payment_type ?? 'zarinpal')
            ->amount((int) $order->total_price)
            ->transactionId($authority)
            ->verify();

        // تغییر وضعیت سفارش به پرداخت موفق
        Order::successPayment($order);

        // ثبت سهم فروشندگان
        $order_details = OrderDetail::query()
            ->where('order_id', $order->id)
            ->get();

        if ($order_details->isNotEmpty()) {
            SellerWalletTransaction::registerSale($order_details);
        }

        $result = 'success';

        // اورراید کردن دانلودها پس از موفقیت تراکنش اولیه
        $downloads = $this->getDownloads($order);

    } catch (\Exception $e) {
        Log::error('Zarinpal Verification Error: ' . $e->getMessage());
        $result = 'failed';
    }

    return view('frontend.shopping_result', compact('order', 'result', 'downloads'));
}

/**
 * 🏆 متد کمکی برای جلوگیری از تکرار کد واکشی دانلودها
 */
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
