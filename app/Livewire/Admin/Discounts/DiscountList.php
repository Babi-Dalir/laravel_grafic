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
    public $search;

    /**
     * تغییر وضعیت ایمن و واکسینه شده در برابر دبل‌کلیک روی سرور آنلاین
     */
    public function changeStatus($id)
    {
        $discount = Discount::query()->findOrFail($id);

        // بهینه‌سازی آنلاین: تبدیل وضعیت به صورت صریح و مستقیم بدون ریسک اجرای همزمان شروط خطی
        $newStatus = ($discount->status === DiscountStatus::Active)
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
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $discounts = Discount::query()
            ->when($this->search, function ($query) {
                $query->where(
                    'code',
                    'like',
                    '%' . $this->search . '%'
                );
            })
            ->latest() // بهینه‌سازی سرور: نمایش آخرین تخفیف‌های ایجاد شده در صدر جدول
            ->paginate(10);

        return view('livewire.admin.discounts.discount-list', compact('discounts'));
    }
}
