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

    // 🟢 اصلاح ساختار: بهینه‌سازی سیستم لایو چت و لایووایر ۳ جهت رندر همزمان نتایج
    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('destroy_gift_cart')]
    public function destroyGiftCart($id)
    {
        GiftCart::destroy($id);

        $gift_carts = $this->getGiftCartsQuery()->paginate(10);
        $this->setPage(min($this->getPage(), $gift_carts->lastPage()));
    }

    /**
     * تغییر وضعیت به همراه گارد ممانعت از فعال‌سازی کارت‌های هدیه منقضی شده
     */
    public function changeStatus($id)
    {
        $gift_cart = GiftCart::query()->findOrFail($id);

        // 🔒 گارد امنیتی روز-محور کارت‌های هدیه
        if ($gift_cart->expiration_date && $gift_cart->expiration_date->isBefore(today())) {
            $this->dispatch('showToastError', message: 'این کارت هدیه منقضی شده و قابل فعال‌سازی نیست.');
            return;
        }

        // 🟢 اصلاح یکدستی انوم
        $newStatus = ($gift_cart->status->value === GiftCartStatus::Active->value)
            ? GiftCartStatus::InActive->value
            : GiftCartStatus::Active->value;

        $gift_cart->update([
            'status' => $newStatus
        ]);
    }

    private function getGiftCartsQuery()
    {
        return GiftCart::query()
            ->when(trim($this->search), function($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('gift_title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->with('user') // ایگر لودینگ (Eager Loading) جهت واکسینه کردن کوئری در برابر باگ N+1
            ->latest();
    }

    public function render()
    {
        $gift_carts = $this->getGiftCartsQuery()->paginate(10);

        return view('livewire.admin.gift-carts.gift-cart-list', compact('gift_carts'));
    }
}
