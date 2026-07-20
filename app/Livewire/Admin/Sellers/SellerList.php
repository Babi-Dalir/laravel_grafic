<?php

namespace App\Livewire\Admin\Sellers;

use App\Enums\SellerStatus;
use App\Models\Seller;
use Livewire\Component;
use Livewire\WithPagination;

class SellerList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    /**
     * شنونده‌های به‌روزرسانی برای جلوگیری از باگ صفحات ثانویه در سرچ
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }


    /**
     * تغییر وضعیت هوشمند فروشنده به همراه همگام‌سازی تاییدیه بانکی و پیام‌های فارسی
     */
    public function changeStatus($id)
    {
        $seller = Seller::query()->find($id);

        if (!$seller) {
            session()->flash('error', 'فروشنده‌ای با این مشخصات یافت نشد.');
            return;
        }

        // ۱. پیدا کردن وضعیت بعدی (چرخه استیت ماشین)
        $nextStatus = match ($seller->status) {
            SellerStatus::Pending->value   => SellerStatus::Active->value,
            SellerStatus::Active->value    => SellerStatus::Rejected->value,
            SellerStatus::Rejected->value  => SellerStatus::Suspended->value,
            SellerStatus::Suspended->value => SellerStatus::Pending->value,
            default                        => SellerStatus::Pending->value,
        };

        // ۲. تعیین وضعیت تاییدیه حساب بانکی و تاریخ تایید
        $isActivating = ($nextStatus === SellerStatus::Active->value);

        // ۳. به‌روزرسانی اتمیک فروشنده
        $seller->update([
            'status'        => $nextStatus,
            'bank_verified' => $isActivating ? true : false, // 👈 اگر فعال شد تایید بانک true می‌شود، در غیر این صورت false
            'verified_at'   => $isActivating ? now() : $seller->verified_at,
        ]);

        // ۴. عنوان فارسی وضعیت جهت نمایش در پیام
        $statusLabel = match ($nextStatus) {
            SellerStatus::Active->value    => 'فعال (و تایید حساب بانکی)',
            SellerStatus::Pending->value   => 'در حال بررسی (معلق)',
            SellerStatus::Rejected->value  => 'رد شده (غیرفعال)',
            SellerStatus::Suspended->value => 'تعلیق شده (غیرمجاز)',
            default                        => 'نامشخص',
        };

        // 🟢 ارسال پیام کاملاً فارسی و شفاف
        session()->flash('message', "وضعیت حساب فروشنده با موفقیت به «{$statusLabel}» تغییر یافت.");
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sellers = Seller::query()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'user_name', 'mobile', 'email', 'image');
            }])
            ->when(filled($this->search), function ($query) {
                // 🟢 حل باگ اتمیک امنیت کوئری: بسته‌بندی تمام orWhereها درون یک لایه منطقی کپسوله شده
                $query->where(function ($q) {
                    $searchTerm = "%{$this->search}%";

                    $q->where('brand_name', 'like', $searchTerm)
                        ->orWhere('national_code', 'like', $searchTerm)
                        ->orWhere('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm)
                        ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('name', 'like', $searchTerm)
                                ->orWhere('mobile', 'like', $searchTerm)
                                ->orWhere('email', 'like', $searchTerm);
                        });
                });
            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.sellers.seller-list', compact('sellers'));
    }
}
