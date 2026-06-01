<?php

namespace App\Livewire\Frontend\Orders;

use App\Enums\CartType;
use App\Models\Discount;
use App\Models\GiftCart;
use App\Models\PaymentType;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Payment extends Component
{
    public $payment_type;
    public $payment_types;
    public $carts;
    public $total_price;
    public $discount_price;

    public function mount()
    {
        $this->payment_type = 'zarinpal';

        $this->payment_types = PaymentType::query()->get();
        $this->carts = UserCart::query()
            ->where('user_id',auth()->user()->id)
            ->where('type',CartType::Main->value)
            ->get();
        $this->total_price = 0;
        $this->discount_price = 0;
        foreach ($this->carts as $cart){
            $product_price = ProductPrice::query()
                ->where('product_id',$cart->product_id)
                ->where('color_id',$cart->color_id)
                ->where('guaranty_id',$cart->guaranty_id)
                ->first();
            $this->total_price += ($product_price->price) * $cart->count;
            $this->discount_price += ($product_price->main_price - $product_price->price) * $cart->count;
        }
    }
    public function render()
    {
        $shop_data = Session::get('shop_data');
        $shop_data['payment_type'] = $this->payment_type;
        $shop_data['discount_code'] = $this->discount_code;
        $shop_data['gift_cart_code'] = $this->gift_cart_code;
        Session::put('shop_data',$shop_data);
        return view('livewire.frontend.orders.payment');
    }
}
