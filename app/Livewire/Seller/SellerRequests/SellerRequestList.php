<?php

namespace App\Livewire\Seller\SellerRequests;

use App\Enums\SellerRequestStatus;
use App\Models\SellerRequest;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SellerRequestList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public function changeStatus($id)
    {
        $seller_request = SellerRequest::query()->find($id);
        if ($seller_request->status == SellerRequestStatus::Pending->value){
            $seller_request->update([
                'status'=>SellerRequestStatus::Approved->value
            ]);
        }elseif ($seller_request->status == SellerRequestStatus::Approved->value){
            $seller_request->update([
                'status'=>SellerRequestStatus::Rejected->value
            ]);
        }elseif ($seller_request->status == SellerRequestStatus::Rejected->value){
            $seller_request->update([
                'status'=>SellerRequestStatus::Pending->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $seller_requests = SellerRequest::query()
            ->whereHas('user',function ($q){
                return $q->where('name','like','%'.$this->search.'%')
                    ->orWhere('mobile','like','%'.$this->search.'%')
                    ->orWhere('email','like','%'.$this->search.'%');
            })
            ->paginate(10);
        return view('livewire.seller.seller-requests.seller-request-list',compact('seller_requests'));
    }
}
