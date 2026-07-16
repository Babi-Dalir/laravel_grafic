<?php

namespace App\Livewire\Admin\Discounts;

use App\Enums\DiscountStatus;
use App\Models\Discount;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🟢 اصلاح ساختار: یکپارچه‌سازی و بهینه‌سازی سیستم لایو جستجو بدون نیاز به متد کمکی فرم
    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * تغییر وضعیت تعاملی به همراه گارد امنیتی عدم احیای کدهای تخفیف مرده
     */
    public function changeStatus($id)
    {
        $discount = Discount::query()->findOrFail($id);

        // 🔒 گارد امنیتی روز-محور کدهای تخفیف
        if ($discount->expiration_date && $discount->expiration_date->isBefore(today())) {
            $this->dispatch('showToastError', message: 'این کد تخفیف منقضی شده و قابل فعال‌سازی نیست.');
            return;
        }

        // 🔒 گارد جلوگیری از فعال‌سازی کد بدون ظرفیت
        if ($discount->remaining_count <= 0) {
            $this->dispatch('showToastError', message: 'ظرفیت استفاده از این کد تخفیف به پایان رسیده است.');
            return;
        }

        // 🟢 اصلاح یکدستی انوم (حتی با وجود کستینگ الکونت، استفاده صریح از ->value ترجیح دارد)
        $newStatus = ($discount->status->value === DiscountStatus::Active->value)
            ? DiscountStatus::InActive->value
            : DiscountStatus::Active->value;

        $discount->update([
            'status' => $newStatus
        ]);
    }

    #[On('destroy_discount')]
    public function destroyDiscount($id)
    {
        Discount::destroy($id);

        $discounts = $this->getDiscountsQuery()->paginate(15);
        $this->setPage(min($this->getPage(), $discounts->lastPage()));
    }

    private function getDiscountsQuery()
    {
        return Discount::query()
            ->when(trim($this->search), function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%');
            })
            ->latest();
    }

    public function render()
    {
        $discounts = $this->getDiscountsQuery()->paginate(15);

        return view('livewire.admin.discounts.discount-list', compact('discounts'));
    }
}
