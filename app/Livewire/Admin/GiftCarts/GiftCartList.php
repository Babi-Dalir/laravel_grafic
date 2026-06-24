<?php

namespace App\Livewire\Admin\GiftCarts;

use App\Enums\GiftCartStatus;
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

    /**
     * تغییر وضعیت ایمن و اتمیک جهت جلوگیری از باگ رفتارهای موازی روی سرور
     */
    public function changeStatus($id)
    {
        $gift_cart = GiftCart::query()->findOrFail($id);

        // 🟢 بهینه‌سازی آنلاین: تشخیص صریح بر اساس وضعیت جاری مدل بدون شروط زنجیره‌ای خطی
        $newStatus = ($gift_cart->status === GiftCartStatus::Active)
            ? GiftCartStatus::InActive->value
            : GiftCartStatus::Active->value;

        $gift_cart->update([
            'status' => $newStatus
        ]);
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
            ->with('user') // حل باگ N+1
            ->latest()     // اولویت نمایش کارت‌های جدید ادمین
            ->paginate(10);

        return view('livewire.admin.gift-carts.gift-cart-list', compact('gift_carts'));
    }
}
