<?php

namespace App\Livewire\Frontend\Carts;

use App\Enums\CartType;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Livewire\Attributes\On;
use Livewire\Component;

class CartsDetail extends Component
{
    public function increaseCart($product_id,$color_id,$guaranty_id)
    {
        $user_cart = UserCart::query()
            ->where('user_id',auth()->user()->id)
            ->where('product_id',$product_id)
            ->where('color_id',$color_id)
            ->where('guaranty_id',$guaranty_id)
            ->first();
        $product_price = ProductPrice::query()
            ->where('product_id',$product_id)
            ->where('color_id',$color_id)
            ->where('guaranty_id',$guaranty_id)
            ->first();
        if ($user_cart && $user_cart->count < $user_cart->product->max_sell && $product_price->count > $user_cart->count){
            $user_cart->update([
                'count'=>$user_cart->count +1
            ]);
        }
        $this->dispatch('deleteProductCart');
    }
    public function decreaseCart($product_id,$color_id,$guaranty_id)
    {
        $user_cart = UserCart::query()
            ->where('user_id',auth()->user()->id)
            ->where('product_id',$product_id)
            ->where('color_id',$color_id)
            ->where('guaranty_id',$guaranty_id)
            ->first();
        if ($user_cart && $user_cart->count >1){
            $user_cart->update([
                'count'=>$user_cart->count -1
            ]);
        }else{
            $user_cart->delete();
        }
        $this->dispatch('deleteProductCart');
    }
    public function moveToReserveCart($cart_id)
    {
        $user_cart = UserCart::query()->find($cart_id);
        $user_cart->update([
            'type'=>CartType::Reserve->value
        ]);
    }
    public function moveToMainCart($cart_id)
    {
        $user_cart = UserCart::query()->find($cart_id);
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
        $user_cart = UserCart::query()->find($cart_id);
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
            ->where('user_id',auth()->id())
            ->where('type',CartType::Main->value)->get();
        $reserve_carts = UserCart::query()->where('type',CartType::Reserve->value)->get();
        $total_price = 0;
        $discount_price = 0;
        foreach ($carts as $cart){
            $product_price = ProductPrice::query()
                ->where('product_id',$cart->product_id)
                ->where('color_id',$cart->color_id)
                ->where('guaranty_id',$cart->guaranty_id)
                ->first();
            $total_price += ($product_price->price) * $cart->count;
            $discount_price += ($product_price->main_price - $product_price->price) * $cart->count;
        }
        return view('livewire.frontend.carts.carts-detail',compact('carts','reserve_carts','total_price','discount_price'));
    }
}
