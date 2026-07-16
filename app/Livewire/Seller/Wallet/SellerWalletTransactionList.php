<?php

namespace App\Livewire\Seller\Wallet;

use App\Enums\TransactionType;
use App\Models\SellerWalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class SellerWalletTransactionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $type = ''; // 🟢 مقداردهی اولیه به صورت رشته خالی برای جلوگیری از باگ‌های آپدیت سلکتور

    /**
     * 🚀 همگام‌سازی لایف‌سایکل لایووایر ۳ جهت بازنشانی صفحه پیجینیشن هنگام سرچ
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * بازنشانی صفحه پیجینیشن هنگام تغییر فیلتر نوع تراکنش
     */
    public function updatedType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $seller = $user->seller;

        // گارد محافظتی: اگر کاربر نقش فروشنده نداشت یا اطلاعاتش ثبت نشده بود
        if (!$seller) {
            abort(403, 'اطلاعات فروشندگی شما یافت نشد.');
        }

        $transactions = SellerWalletTransaction::query()
            // 🟢 بهینه‌سازی زنجیره روابط جهت جلوگیری از باگ N+1 و افزایش سرعت لود به میلی‌ثانیه
            ->with([
                'order.orderDetails.product:id,name',
            ])
            ->where('seller_id', $seller->id)
            // 🟢 رفع باگ امنیتی IDOR با گروه‌بندی شرط‌های جستجو (Logical Grouping)
            ->when(filled($this->search), function ($q) {
                $q->where(function ($innerQuery) {
                    $searchTerm = "%{$this->search}%";
                    $innerQuery->where('description', 'like', $searchTerm)
                        ->orWhere('code', 'like', $searchTerm)
                        ->orWhere('reference_id', 'like', $searchTerm);
                });
            })
            // 🟢 فیلتر دقیق نوع تراکنش مبتنی بر انوم سیستم
            ->when(filled($this->type), function ($q) {
                $q->where('type', $this->type);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.seller.wallet.seller-wallet-transaction-list', [
            'transactions' => $transactions,
            'seller' => $seller,
            'pending' => $seller->pending_balance ?? 0,
            'paid' => $seller->paid_balance ?? 0,
            'types' => TransactionType::cases(),
            'canWithdraw' => ($seller->pending_balance ?? 0) >= 100000,
        ]);
    }
}
