<?php

namespace App\Livewire\Frontend\Profiles;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;
    public function render()
    {
        $user = auth()->user();
        $orders = Order::query()->where('user_id', $user->id)
            ->latest()
            ->paginate(1);
        return view('livewire.frontend.profiles.order-list',compact('orders'));
    }
}
