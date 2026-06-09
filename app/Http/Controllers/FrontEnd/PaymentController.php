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
use App\Models\ProductPrice;
use App\Models\SellerWalletTransaction;
use App\Models\UserCart;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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

            $total_price += $product->final_price;

            $product_discount_price +=
                ($product->main_price - $product->final_price);
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

            $order = Order::createOrder(
                $user,
                $total_price,
                $shop_data,
                $total_discount_price,
                $gift_cart_code_price
            );

            $order_details = [];

            foreach ($carts as $cart) {

                $product = Product::query()
                    ->where('id', $cart->product_id)
                    ->first();

                if (!$product) {
                    throw new \Exception('قیمت محصول یافت نشد');
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

            Log::error($e->getMessage());

            return back()->with('error', 'خطا در ایجاد سفارش');
        }

        /*
        |--------------------------------------------------------------------------
        | Offline Payment
        |--------------------------------------------------------------------------
        */

        if ($shop_data['payment_type'] === 'offline') {

            DB::beginTransaction();

            try {

                Order::successPayment(
                    $order,
                    $order_details,
                    $shop_data['discount_code'] ?? null,
                    $shop_data['gift_cart_code'] ?? null
                );

                SellerWalletTransaction::registerSale($order_details);

                DB::commit();

                $result = 'success';

            } catch (\Exception $e) {

                DB::rollBack();

                Log::error($e->getMessage());

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
        | Online Payment
        |--------------------------------------------------------------------------
        */

        return Payment::via($shop_data['payment_type'])
            ->purchase(
                (new Invoice)->amount($total_price),
                function ($driver, $transactionId) use ($order) {

                    $order->update([
                        'transaction_id' => $transactionId
                    ]);
                }
            )
            ->pay()
            ->render();
    }

    public function callback(Request $request)
    {
        $authority = $request->Authority;

        $order = Order::query()
            ->where('transaction_id', $authority)
            ->first();

        if (!$order) {

            return view('frontend.shopping_result', [
                'result' => 'failed',
                'order' => null
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | جلوگیری از پرداخت تکراری
        |--------------------------------------------------------------------------
        */

        if ($order->status == OrderStatus::Payed->value) {

            return view('frontend.shopping_result', [
                'order' => $order,
                'result' => 'success'
            ]);
        }

        if ($request->Status !== 'OK') {

            return view('frontend.shopping_result', [
                'order' => $order,
                'result' => 'failed'
            ]);
        }

        DB::beginTransaction();

        try {

            $order_details = OrderDetail::query()
                ->where('order_id', $order->id)
                ->get();

            Order::successPayment(
                $order,
                $order_details,
                $order->discount_code,
                $order->gift_cart_code
            );

            SellerWalletTransaction::registerSale($order_details);

            DB::commit();

            $result = 'success';

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e->getMessage());

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
}
