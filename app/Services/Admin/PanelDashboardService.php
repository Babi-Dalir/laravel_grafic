<?php

namespace App\Services\Admin;

use App\Enums\SellerStatus;
use App\Enums\WalletTransactionStatus;
use App\Enums\OrderStatus; // 🟢 انوم وضعیت سفارش اضافه شد
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Seller;
use App\Models\SellerSettlement;
use App\Models\SellerWalletTransaction;
use Illuminate\Support\Facades\Cache;

class PanelDashboardService
{
    public function kpis(): array
    {
        return Cache::remember('admin.panel.kpis', 60, function () {
            $now = now();
            $payedStatus = OrderStatus::Payed->value;

            // 🟢 پچ سفارشات امروز: فقط سفارشات پرداخت‌شده امروز
            $todayOrders = Order::where('status', $payedStatus)
                ->whereDate('created_at', $now)
                ->count();

            // 🟢 پچ فروش امروز بازار: فقط اقلامی که سفارش آنها پرداخت موفق بوده است
            $todaySales = OrderDetail::whereDate('created_at', $now)
                ->where('seller_share', '>', 0)
                ->whereHas('order', function ($q) use ($payedStatus) {
                    $q->where('status', $payedStatus);
                })
                ->sum('price');

            // 🟢 پچ فروش ماه جاری بازار: منوط به پرداخت موفق سفارش
            $monthSales = OrderDetail::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->where('seller_share', '>', 0)
                ->whereHas('order', function ($q) use ($payedStatus) {
                    $q->where('status', $payedStatus);
                })
                ->sum('price');

            // 🟢 پچ محاسبات سود ماهانه سایت: فقط فاکتورهای پرداخت موفق
            $monthStats = OrderDetail::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->whereHas('order', function ($q) use ($payedStatus) {
                    $q->where('status', $payedStatus);
                })
                ->selectRaw('SUM(site_share) as gross_share, SUM(platform_subsidy) as subsidy')
                ->first();

            $siteIncomeMonth = ($monthStats->gross_share ?? 0) - ($monthStats->subsidy ?? 0);

            // 🟢 پچ کل سود تاریخی سایت: فقط فاکتورهای پرداخت موفق تاریخ سایت
            $totalStats = OrderDetail::whereHas('order', function ($q) use ($payedStatus) {
                $q->where('status', $payedStatus);
            })
                ->selectRaw('SUM(site_share) as gross_share, SUM(platform_subsidy) as subsidy')
                ->first();

            $totalSiteIncome = ($totalStats->gross_share ?? 0) - ($totalStats->subsidy ?? 0);

            return [
                'today_orders'       => $todayOrders,
                'today_sales'        => $todaySales,
                'month_sales'        => $monthSales,
                'site_income_month'  => $siteIncomeMonth,
                'total_site_income'  => $totalSiteIncome,
            ];
        });
    }

    public function sellers(): array
    {
        return Cache::remember('admin.panel.sellers', 120, function () {
            return [
                'active_sellers'  => Seller::where('status', SellerStatus::Active->value)->count(),
                'pending_balance' => SellerWalletTransaction::where('status', WalletTransactionStatus::Pending->value)->sum('amount'),
                'settled_balance' => SellerWalletTransaction::where('status', WalletTransactionStatus::Settled->value)->sum('amount'),
            ];
        });
    }

    public function latest(): array
    {
        return Cache::remember('admin.panel.latest', 30, function () {
            $payedStatus = OrderStatus::Payed->value;

            return [
                'latest_settlements' => SellerSettlement::with(['seller.user'])
                    ->latest()
                    ->take(10)
                    ->get(),

                // 🟢 پچ جدول آخرین سفارش‌ها: فقط نمایش آخرین سفارش‌های "پرداخت‌شده"
                'latest_orders' => Order::with('user')
                    ->where('status', $payedStatus)
                    ->latest()
                    ->take(10)
                    ->get(),
            ];
        });
    }

    public function monthlySalesChart(): array
    {
        return Cache::remember('admin.panel.monthly_sales', 120, function () {
            $year = now()->year;
            $payedStatus = OrderStatus::Payed->value;

            // 🟢 پچ نمودار سالانه: فقط مبالغِ فاکتورهای پرداخت موفق روی نمودار بروند
            $rows = OrderDetail::selectRaw('
                    MONTH(created_at) as month,
                    SUM(price) as total
                ')
                ->whereYear('created_at', $year)
                ->whereHas('order', function ($q) use ($payedStatus) {
                    $q->where('status', $payedStatus);
                })
                ->groupBy('month')
                ->pluck('total', 'month');

            $months = [];
            $sales = [];

            for ($i = 1; $i <= 12; $i++) {
                $months[] = "ماه $i";
                $sales[] = $rows[$i] ?? 0;
            }

            return [
                'months' => $months,
                'sales'  => $sales,
            ];
        });
    }

    public function insights(): array
    {
        return Cache::remember('admin.panel.insights', 60, function () {
            $today = now();
            $yesterday = now()->subDay();
            $payedStatus = OrderStatus::Payed->value;

            // 🟢 پچ ویجت رشد روزانه: مبنا قرار دادن سفارشات موفق امروز و دیروز
            $todaySales = OrderDetail::whereDate('created_at', $today)
                ->whereHas('order', function ($q) use ($payedStatus) {
                    $q->where('status', $payedStatus);
                })
                ->sum('price');

            $yesterdaySales = OrderDetail::whereDate('created_at', $yesterday)
                ->whereHas('order', function ($q) use ($payedStatus) {
                    $q->where('status', $payedStatus);
                })
                ->sum('price');

            $growth = $yesterdaySales > 0
                ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100
                : 0;

            return [
                'sales_growth_today' => round($growth, 2),
                'trend'              => $growth >= 0 ? 'up' : 'down',
            ];
        });
    }

    public function buildDashboard(): array
    {
        return [
            'kpis'     => $this->kpis(),
            'sellers'  => $this->sellers(),
            'latest'   => $this->latest(),
            'chart'    => $this->monthlySalesChart(),
            'insights' => $this->insights(),
        ];
    }

    public function clearCache(): void
    {
        Cache::forget('admin.panel.kpis');
        Cache::forget('admin.panel.sellers');
        Cache::forget('admin.panel.latest');
        Cache::forget('admin.panel.monthly_sales');
        Cache::forget('admin.panel.insights');
    }
}
