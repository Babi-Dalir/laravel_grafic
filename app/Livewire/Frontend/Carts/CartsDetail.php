<?php

namespace App\Livewire\Frontend\Carts;

use App\Enums\CartType;
use App\Models\Discount;
use App\Models\GiftCart;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class CartsDetail extends Component
{
    public $total_price = 0;
    public $discount_price = 0;

    public $discount_code = '';
    public $gift_cart_code = '';

    public $discount_code_price = 0;
    public $gift_cart_price = 0;
    public function submitPayment()
    {
        $this->validate();
        Session::put('shop_data',[
            'discount_code'  => $this->discount_code,
            'gift_cart_code' => $this->gift_cart_code,
        ]);

        return redirect()->route('payment');
    }
    public function discountCode()
    {
        $discount = Discount::query()
            ->where('code', $this->discount_code)
            ->where('discount', '>', 0)
            ->where('expiration_date', '>=', now())
            ->first();

        if ($discount) {

            $this->discount_code_price = $discount->discount;

            session()->flash(
                'success_discount',
                'کد تخفیف ثبت شد'
            );

        } else {

            $this->discount_code_price = 0;

            session()->flash(
                'warning_discount',
                'کد تخفیف اشتباه است'
            );
        }
    }

    public function giftCartCode()
    {
        $gift_cart = GiftCart::query()
            ->where('code', $this->gift_cart_code)
            ->where('user_id', auth()->id())
            ->where('balance', '>', 0)
            ->where('expiration_date', '>=', now())
            ->first();

        if ($gift_cart) {

            $this->gift_cart_price = $gift_cart->balance;

            session()->flash(
                'success_gift_cart',
                'کد کارت هدیه ثبت شد'
            );

        } else {

            $this->gift_cart_price = 0;

            session()->flash(
                'warning_gift_cart',
                'کد کارت هدیه اشتباه است'
            );
        }
    }
    public function moveToReserveCart($cart_id)
    {
        $user_cart = UserCart::query()
            ->where('id', $cart_id)
            ->where('user_id', auth()->id())
            ->first();
        if(!$user_cart){
            return;
        }
        $user_cart->update([
            'type'=>CartType::Reserve->value
        ]);
    }
    public function moveToMainCart($cart_id)
    {
        $user_cart = UserCart::query()
            ->where('id', $cart_id)
            ->where('user_id', auth()->id())
            ->first();
        if(!$user_cart){
            return;
        }
        $user_cart->update([
            'type'=>CartType::Main->value
        ]);
    }
    public function moveToAllMainCart()
    {
        $user_carts = UserCart::query()
            ->where('user_id',auth()->user()->id)
            ->where('type',CartType::Reserve->value)->get();
        foreach ($user_carts as $user_cart){
            $user_cart->update([
                'type'=>CartType::Main->value
            ]);
        }
    }
    public function deleteCart($cart_id)
    {
        $user_cart = UserCart::query()
            ->where('id', $cart_id)
            ->where('user_id', auth()->id())
            ->first();
        if(!$user_cart){
            return;
        }
        $user_cart->delete();
        $this->dispatch('deleteProductCart');
    }
    #[On('deleteProductCart')]
    public function refreshCarts()
    {
        //برای رفرش شدن سبد خرید
    }
    public function render()
    {
        $carts = UserCart::query()
            ->with('product')
            ->where('user_id',auth()->id())
            ->where('type',CartType::Main->value)
            ->get();
        $reserve_carts = UserCart::query()
            ->with('product')
            ->where('user_id', auth()->id())
            ->where('type', CartType::Reserve->value)
            ->get();
        $this->total_price = 0;
        $this->discount_price = 0;

        foreach ($carts as $cart) {
            $this->total_price += $cart->product->final_price;
            $this->discount_price += (
                $cart->product->main_price -
                $cart->product->final_price
            );
        }

        $final_price = $this->total_price - $this->discount_code_price - $this->gift_cart_price;
        $final_price = max($final_price, 0);
        return view('livewire.frontend.carts.carts-detail', [
            'carts' => $carts,
            'reserve_carts' => $reserve_carts,
            'total_price' => $this->total_price,
            'discount_price' => $this->discount_price,
            'final_price' => max($final_price, 0),
        ]);
    }
}
