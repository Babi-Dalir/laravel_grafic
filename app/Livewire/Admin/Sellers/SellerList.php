<?php

namespace App\Livewire\Admin\Sellers;

use App\Enums\CompanyStatus;
use App\Enums\SellerStatus;
use App\Enums\UserStatus;
use App\Models\Seller;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SellerList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public function changeStatus($id)
    {
        $seller = Seller::query()->find($id);
        if ($seller->status == SellerStatus::Pending->value){
            $seller->update([
                'status'=>SellerStatus::Active->value
            ]);
        }elseif ($seller->status == SellerStatus::Active->value){
            $seller->update([
                'status'=>SellerStatus::Rejected->value
            ]);
        }elseif ($seller->status == SellerStatus::Rejected->value){
            $seller->update([
                'status'=>SellerStatus::Suspended->value
            ]);
        }
        elseif ($seller->status == SellerStatus::Suspended->value){
            $seller->update([
                'status'=>SellerStatus::Pending->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $sellers = Seller::query()
            ->with('user')
            ->where('brand_name', 'like', "%{$this->search}%")
            ->orWhere('national_code', 'like', "%{$this->search}%")
            ->orWhere('first_name', 'like', "%{$this->search}%")
            ->orWhereHas('user',function ($q){
                return $q->where('name','like','%'.$this->search.'%')
                    ->orWhere('mobile','like','%'.$this->search.'%')
                    ->orWhere('email','like','%'.$this->search.'%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.sellers.seller-list', compact('sellers'));
    }
}
