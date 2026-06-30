<?php

namespace App\Livewire\Seller\Settlements;

use App\Enums\SettlementStatus;
use App\Models\SellerSettlement;
use App\Services\SettlementManager;
use Livewire\Component;
use Livewire\WithPagination;

class SettlementList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    /**
     * 🚀 اصلاح بر اساس لایف‌سایکل لایووایر ۳
     * به محض تغییر رشته سرچ، صفحه پجینیشن فوراً ریست می‌شود تا دیتای سرچ ادمین گم نشود.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * متد ثبت پرداخت با کنترل استیت مالی
     */
    public function pay($settlementId)
    {
        $settlement = SellerSettlement::findOrFail($settlementId);

        // گارد محافظتی بک‌اند: اگر فاکتور قبلاً پرداخت شده باشد، رکوئست تکراری مکرر را ریجکت کن
        if ($settlement->status === SettlementStatus::Paid || $settlement->status === SettlementStatus::Paid->value) {
            session()->flash('error', 'این فاکتور تسویه حساب قبلاً پرداخت شده است.');
            return;
        }

        // اجرای عملیات از طریق سرفصل مدیریت مالی پروژه‌ گرافیک
        SettlementManager::markAsPaid(
            $settlement,
            auth()->id()
        );

        session()->flash('success', 'واریز وجه و تسویه حساب با موفقیت در سیستم ثبت شد.');
    }

    public function render()
    {
        $settlements = SellerSettlement::query()
            // واکشی عمیق و اتمیک روابط جهت بهینه‌سازی کامل پهنای باند رم سرور
            ->with([
                'seller:id,user_id,first_name,last_name,brand_name',
                'seller.user:id,mobile',
                'user:id,name'
            ])
            ->when(filled($this->search), function ($q) {
                $q->whereHas('seller', function ($sellerQuery) {
                    $searchTerm = "%{$this->search}%";
                    $sellerQuery->where('brand_name', 'like', $searchTerm)
                        ->orWhere('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm);
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.seller.settlements.settlement-list', [
            'settlements' => $settlements,
        ]);
    }
}
