<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\CartType;
use App\Enums\OrderDetailStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Discount;
use App\Models\GiftCart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\UserCart;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class PaymentController extends Controller
{
    public function payment()
    {
        $total_price = 0;
        $discount_code_price = 0;
        $gif_cart_code_price = 0;
        $order_details = [];

        $shop_data = Session::get('shop_data');
        $user = auth()->user();

        $address = Address::getUserAddress($user);

        $carts = UserCart::getUserCart($user);

        $total_price = ProductPrice::calculateTotalPriceInCart($carts,$total_price);
        //discount
        if ($shop_data['discount_code']) {
            $result = Discount::calculateDiscount($shop_data, $total_price, $discount_code_price);
            $total_price = $result['total_price'];
            $discount_code_price = $result['discount_code_price'];
        }

        //gift_cart
        if ($shop_data['gift_cart_code']) {
            $result = GiftCart::calculateGiftCart($shop_data, $total_price, $gif_cart_code_price);
            $total_price = $result['total_price'];
            $gif_cart_code_price = $result['gif_cart_code_price'];
        }

        $order = Order::createOrder($user, $address, $total_price, $shop_data, $discount_code_price, $gif_cart_code_price);

        foreach ($carts as $cart) {
            $product_price = ProductPrice::query()
                ->where('product_id', $cart->product_id)
                ->where('color_id', $cart->color_id)
                ->where('guaranty_id', $cart->guaranty_id)
                ->first();
            $order_details[] = OrderDetail::createOrderDetail($order, $cart, $product_price);
        }
        if ($shop_data['payment_type'] == 'offline') {
            DB::beginTransaction();
            try {
                Order::successPayment($order, $order_details,$shop_data['discount_code'],$shop_data['gift_cart_code']);
                $result = "success";
                DB::commit();

                return view('frontend.shopping_result', compact('order', 'result'));

            } catch (\Exception $exception) {

                DB::rollBack();
                $result = "failed";

                return view('frontend.shopping_result', compact('order', 'result'));
            }
        } else {
            return Payment::via($shop_data['payment_type'])->purchase(
                (new Invoice)->amount($total_price), function ($driver, $transactionId) use ($order) {
                $order->update([
                    'transaction_id' => $transactionId
                ]);
            }
            )->pay()->render();
        }
    }

    public function callback(Request $request)
    {
        $authority = $request->Authority;
        $order = Order::query()->where('transaction_id', $authority)->first();
        $order_details = OrderDetail::query()->where('order_id', $order->id)->get();
        if ($request->Status == "OK") {
            DB::beginTransaction();
            try {
                Order::successPayment($order, $order_details,$order->discount_code,$order->gif_cart_code);
                UserTransaction::soldProductBySeller($order_details);
                $result = "success";
                DB::commit();

                return view('frontend.shopping_result', compact('order', 'result'));

            } catch (\Exception $exception) {

                DB::rollBack();
                $result = "failed";

                return view('frontend.shopping_result', compact('order', 'result'));
            }
        } else {
            $result = "failed";

            return view('frontend.shopping_result', compact('order', 'result'));

        }

    }
}
