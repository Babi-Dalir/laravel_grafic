<?php

namespace App\Livewire\Admin\GiftCarts;

use App\Enums\DiscountStatus;
use App\Models\Discount;
use App\Models\GiftCart;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class GiftCartList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;

    #[On('destroy_gift_cart')]
    public function destroyGiftCart($id)
    {
        GiftCart::destroy($id);
    }


    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $gift_carts = GiftCart::query()
            ->whereHas('user',function ($q){
                return $q->where('name','like','%'.$this->search.'%');
            })
            ->orWhere('code','like','%'.$this->search.'%')
            ->orWhere('gift_title','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.gift-carts.gift-cart-list',compact('gift_carts'));
    }
}
