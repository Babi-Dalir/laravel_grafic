<?php

namespace App\Livewire\Admin\GiftCarts;

use App\Enums\DiscountStatus;
use App\Enums\GiftCartStatus;
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
    public function changeStatus($id)
    {
        $gift_cart = GiftCart::query()->find($id);
        if ($gift_cart->status == GiftCartStatus::Active->value){
            $gift_cart->update([
                'status'=>GiftCartStatus::InActive->value
            ]);
        }elseif ($gift_cart->status == GiftCartStatus::InActive->value){
            $gift_cart->update([
                'status'=>GiftCartStatus::Active->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $gift_carts = GiftCart::query()
            ->where(function($query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('gift_title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->with('user') // برای جلوگیری از N+1
            ->paginate(10);

        return view('livewire.admin.gift-carts.gift-cart-list', compact('gift_carts'));
    }
}
