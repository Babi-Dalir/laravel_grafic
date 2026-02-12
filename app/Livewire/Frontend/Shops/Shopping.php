<?php

namespace App\Livewire\Frontend\Shops;

use App\Enums\CartType;
use App\Models\Address;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class Shopping extends Component
{
    public $send_type;
    public $receive_time;
    public $factor = false;
    public $receive_day;
    public $selected_address;
    public $send_price;
    public $send_time;
    public $selected_day_index = 0;
    public $carts;
    public $total_price;
    public $discount_price;

    protected $rules = [
        'send_type' => 'required',
        'receive_time' => 'required|sometimes'
    ];
    #[On('refreshAddressList')]
    public function refreshAddressList()
    {
        $this->dispatch('$refresh');
    }

    public function mount()
    {
        $this->selected_day_index = 0;
        $this->send_type = "usual";
        $this->receive_time = "9-13";

        $this->carts = UserCart::query()
            ->where('user_id', auth()->user()->id)
            ->where('type', CartType::Main->value)->get();
        $this->total_price = 0;
        $this->discount_price = 0;
        foreach ($this->carts as $cart) {
            $product_price = ProductPrice::query()
                ->where('product_id', $cart->product_id)
                ->where('color_id', $cart->color_id)
                ->where('guaranty_id', $cart->guaranty_id)
                ->first();
            $this->total_price += ($product_price->price) * $cart->count;
            $this->discount_price += ($product_price->main_price - $product_price->price) * $cart->count;
        }
    }

    public function receiveDay($i)
    {
        $this->selected_day_index = $i;
        $this->receive_day = Carbon::now()->addDays($i + $this->send_time);
    }

    public function submitCountinueOrder()
    {
        $this->validate();
        $shop_data = [];
        $shop_data['send_type'] = $this->send_type;
        $shop_data['receive_time'] = $this->receive_time;
        $shop_data['factor'] = $this->factor;
        $shop_data['receive_day'] = $this->receive_day;
        Session::put('shop_data', $shop_data);
        return redirect()->route('user.shopping.payment');
    }

    public function render()
    {
        $addresses = Address::query()
            ->where('user_id', auth()->user()->id)
            ->orderByDesc('is_default')
            ->get();
        $address = Address::query()
            ->where('user_id', auth()->user()->id)
            ->where('is_default', true)
            ->first();
        if ($address){
            $this->selected_address = $address;
            $this->send_price = $this->selected_address->city->send_price;
            $this->send_time = $this->selected_address->city->send_time;

            if (Carbon::now()->addDays($this->send_time)->dayOfWeek == CarbonInterface::FRIDAY){
                $this->receive_day = Carbon::now()->addDays($this->send_time +1);
                $this->selected_day_index = 0;
            }
        }

        return view('livewire.frontend.shops.shopping', compact( 'addresses'));
    }
}
