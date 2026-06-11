<?php

namespace App\Livewire\Admin\Order;

use App\Enums\OrderDetailStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use Livewire\Component;
use Livewire\WithPagination;

class OrderDetails extends Component
{
    public $order;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public function changeOrderDetailStatus($id)
    {
        $order_detail = OrderDetail::query()->find($id);
        if ($order_detail->status == OrderDetailStatus::Waiting->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Paid->value
            ]);
        }elseif ($order_detail->status == OrderDetailStatus::Paid->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Downloaded->value
            ]);
        }
        elseif ($order_detail->status == OrderDetailStatus::Downloaded->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Refunded->value
            ]);
        }
        elseif ($order_detail->status == OrderDetailStatus::Refunded->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Waiting->value
            ]);
        }
    }
    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $order_details = OrderDetail::query()
            ->where('order_id',$this->order->id)
            ->whereHas('product',function ($q){
                return $q->where('name','like','%'.$this->search.'%');
            })
            ->paginate(10);
        return view('livewire.admin.order.order-details',compact('order_details'));
    }
}
