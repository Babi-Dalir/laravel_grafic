<?php

namespace App\Livewire\Seller\Settlements;

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
     * 🟢 حل باگ ریست پجینیشن: به محض تایپ کاربر، صفحه خودکار یک می‌شود تا دیتای سرچ گم نشود
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * متد ثبت پرداخت با کنترل استیت
     */
    public function pay($settlementId)
    {
        $settlement = SellerSettlement::findOrFail($settlementId);

        // اجرای عملیات از طریق سرفصل مدیریت مالی
        SettlementManager::markAsPaid(
            $settlement,
            auth()->id()
        );

        session()->flash('success', 'واریز وجه و تسویه حساب با موفقیت در سیستم ثبت شد.');
    }

    public function render()
    {
        $settlements = SellerSettlement::query()
            // 🟢 حل مشکل N+1 با واکشی عمیق و بهینه روابط دیتابیس (فقط فیلدهای مورد نیاز)
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
