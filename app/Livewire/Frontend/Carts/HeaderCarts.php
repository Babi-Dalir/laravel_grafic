<?php

namespace App\Livewire\Frontend\Carts;

use App\Enums\CartType;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Livewire\Attributes\On;
use Livewire\Component;

class HeaderCarts extends Component
{
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

        $total_price = 0;
        foreach ($carts as $cart){
            $product_price = ProductPrice::query()
                ->where('product_id',$cart->product_id)
                ->where('color_id',$cart->color_id)
                ->where('guaranty_id',$cart->guaranty_id)
                ->first();
            $total_price += ($product_price->price) * $cart->count;
        }
        return view('livewire.frontend.carts.header-carts',compact('carts','total_price'));
    }
}
