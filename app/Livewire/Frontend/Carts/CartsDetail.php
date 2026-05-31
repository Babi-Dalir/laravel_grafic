<?php

namespace App\Livewire\Frontend\Carts;

use App\Enums\CartType;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Livewire\Attributes\On;
use Livewire\Component;

class CartsDetail extends Component
{
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
        $total_price = 0;
        $discount_price = 0;
        foreach ($carts as $cart){
            $total_price += $cart->product->final_price;
            $discount_price += ($cart->product->main_price - $cart->product->final_price);
        }
        return view('livewire.frontend.carts.carts-detail',compact('carts','reserve_carts','total_price','discount_price'));
    }
}
