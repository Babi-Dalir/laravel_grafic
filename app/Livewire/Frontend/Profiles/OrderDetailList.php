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
        // 🟢 امنیت سکیوریتی: قفل کردن کوئری روی کاربر لاگین شده برای جلوگیری از هک امنیتی IDOR
        $order = Order::query()
            ->where('user_id', auth()->id())
            ->find($this->order_id);

        if (!$order) {
            abort(404);
        }

        $order_details = OrderDetail::query()
            ->with(['product', 'download'])
            ->where('order_id', $this->order_id)
            ->latest()
            ->paginate(15);

        return view('livewire.frontend.profiles.order-detail-list', compact('order', 'order_details'));
    }
}
