<?php

namespace App\Livewire\Admin\Discounts;

use App\Enums\DiscountStatus;
use App\Enums\UserStatus;
use App\Models\Banner;
use App\Models\Discount;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountList extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public function changeStatus($id)
    {
        $discount = Discount::query()->find($id);
        if ($discount->status == DiscountStatus::Active->value){
            $discount->update([
                'status'=>DiscountStatus::InActive->value
            ]);
        }elseif ($discount->status == DiscountStatus::InActive->value){
            $discount->update([
                'status'=>DiscountStatus::Active->value
            ]);
        }
    }
    #[On('destroy_discount')]
    public function destroyDiscount($id)
    {
        Discount::destroy($id);
    }


    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $discounts = Discount::query()
            ->where('code','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.discounts.discount-list',compact('discounts'));
    }
}
