<?php

namespace App\Livewire\Admin\Financial;

use App\Enums\OrderStatus;
use App\Models\OrderDetail;
use Livewire\Component;
use Livewire\WithPagination;

class FinancialLedgerList extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'all'; // all, market, website

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'all'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $payedStatus = OrderStatus::Payed->value;

        // ۱. واکشی دیتای فیلتر شده با گارد پرداخت موفق
        $ledgerQuery = OrderDetail::query()
            ->with(['product.user', 'order'])
            ->whereHas('order', function ($q) use ($payedStatus) {
                $q->where('status', $payedStatus);
            });

        // ۲. اعمال فیلتر جستجوی زنده (بر اساس نام محصول یا کد سفارش)
        if ($this->search) {
            $ledgerQuery->where(function ($q) {
                $q->whereHas('product', function ($pq) {
                    $pq->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('order', function ($oq) {
                    $oq->where('order_code', 'like', '%' . $this->search . '%');
                });
            });
        }

        // ۳. اعمال فیلتر تب‌ها (تفکیک محصولات سایت و مارکت‌پلیس)
        if ($this->activeTab === 'market') {
            $ledgerQuery->where('seller_share', '>', 0);
        } elseif ($this->activeTab === 'website') {
            $ledgerQuery->where('seller_share', 0);
        }

        $records = $ledgerQuery->latest()->paginate(15);

        // ۴. محاسبه آنی خلاصه وضعیت لجر (فقط مبالغ فیلتر شده جاری)
        $summaryQuery = OrderDetail::query()
            ->whereHas('order', function ($q) use ($payedStatus) {
                $q->where('status', $payedStatus);
            });

        if ($this->activeTab === 'market') {
            $summaryQuery->where('seller_share', '>', 0);
        } elseif ($this->activeTab === 'website') {
            $summaryQuery->where('seller_share', 0);
        }

        $totals = $summaryQuery->selectRaw('
            SUM(price) as total_turnover,
            SUM(site_share) as total_site_share,
            SUM(platform_subsidy) as total_subsidy,
            SUM(seller_share) as total_seller_share
        ')->first();

        return view('livewire.admin.financial.financial-ledger-list', [
            'records' => $records,
            'totals'  => $totals
        ])->extends('admin.layouts.master'); // اکستند کردن مسترپیج ادمین
    }
}
