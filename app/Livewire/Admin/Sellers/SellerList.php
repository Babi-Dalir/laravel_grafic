<?php

namespace App\Livewire\Admin\Sellers;

use App\Enums\CompanyStatus;
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
        if ($seller->status == CompanyStatus::Active->value){
            $seller->update([
                'status'=>CompanyStatus::Banned->value
            ]);
        }elseif ($seller->status == CompanyStatus::Banned->value){
            $seller->update([
                'status'=>CompanyStatus::Request->value
            ]);
        }elseif ($seller->status == CompanyStatus::Request->value){
            $seller->update([
                'status'=>CompanyStatus::Active->value
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
            ->where('company_name','like','%'.$this->search.'%')
            ->orWhere('company_economy_code','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.sellers.seller-list',compact('sellers'));
    }
}
