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
        if ($order_detail->status == OrderDetailStatus::Processing->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Waiting->value
            ]);
        }elseif ($order_detail->status == OrderDetailStatus::Waiting->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Received->value
            ]);
        }
        elseif ($order_detail->status == OrderDetailStatus::Received->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Rejected->value
            ]);
        }
        elseif ($order_detail->status == OrderDetailStatus::Rejected->value){
            $order_detail->update([
                'status'=>OrderDetailStatus::Processing->value
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
            ->whereHas('product',function ($q){
                return $q->where('name','like','%'.$this->search.'%');
            })
            ->paginate(10);
        return view('livewire.admin.order.order-details',compact('order_details'));
    }
}
