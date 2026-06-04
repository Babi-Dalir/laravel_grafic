<?php

namespace App\Livewire\Frontend\Profiles;

use App\Models\Order;
use App\Models\OrderDetail;
use Livewire\Component;
use Livewire\WithPagination;

class OrderDetailList extends Component
{
    use WithPagination;
    public $order_id;

    public function render()
    {
        $order = Order::query()->find($this->order_id);
        $order_details = OrderDetail::query()->where('order_id', $this->order_id)->paginate(1);

        return view('livewire.frontend.profiles.order-detail-list', compact('order', 'order_details'));
    }
}
