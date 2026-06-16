<?php

namespace App\Livewire\Frontend\Carts;

use App\Enums\CartType;
use App\Enums\ProductStatus;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class HeaderCarts extends Component
{
    public $total_price = 0;
    public $discount_price = 0;
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
            ->with(['product' => function ($q) {
                $q->where('status', ProductStatus::Approved->value);
            }])
            ->where('user_id', auth()->id())
            ->where('type', CartType::Main->value)
            ->get()
            ->filter(fn($cart) => $cart->product);

        $this->total_price = 0;
        $this->discount_price = 0;

        foreach ($carts as $cart) {

            $this->total_price += $cart->product->final_price;

            $this->discount_price += (
                $cart->product->main_price -
                $cart->product->final_price
            );
        }

        $final_price = max($this->total_price, 0);

        return view('livewire.frontend.carts.header-carts', [
            'carts' => $carts,
            'total_price' => $this->total_price,
            'discount_price' => $this->discount_price,
            'final_price' => $final_price,
        ]);
    }
}
