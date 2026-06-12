<?php

namespace App\Livewire\Seller\SellerRequests;

use App\Enums\SellerRequestStatus;
use App\Enums\SellerStatus;
use App\Models\Seller;
use App\Models\SellerRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SellerRequestList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public $sellerRequestId;
    public $admin_note;

    public function approveRequest($id)
    {
        DB::transaction(function () use ($id) {

            $sellerRequest = SellerRequest::with('user')->findOrFail($id);

            $user = $sellerRequest->user;

            // 1. آپدیت درخواست
            $sellerRequest->update([
                'status' => SellerRequestStatus::Approved->value,
                'reviewed_at' => now(),
                'admin_note' => null,
            ]);

            // 2. دادن نقش فروشنده
            $user->assignRole('فروشنده');

            // 3. ساخت رکورد seller (اگر وجود ندارد)
            if (! $user->seller) {
                Seller::query()->create([
                    'user_id' => $user->id,
                    'first_name' => $user->name, // یا از request
                    'status' => SellerStatus::Pending->value,
                ]);
            }

        });

        session()->flash(
            'message',
            'درخواست فروشندگی با موفقیت تایید شد.'
        );
    }

    public function rejectRequest()
    {
        $request = SellerRequest::findOrFail($this->sellerRequestId);

        $request->update([
            'status' => SellerRequestStatus::Rejected->value,
            'admin_note' => $this->admin_note,
            'reviewed_at' => now(),
        ]);

        $this->reset([
            'sellerRequestId',
            'admin_note'
        ]);

        $this->dispatch('closeRejectModal');

        session()->flash(
            'message',
            'درخواست با موفقیت رد شد.'
        );
    }
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
