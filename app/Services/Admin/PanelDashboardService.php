<?php

namespace App\Services\Admin;

use App\Enums\SellerStatus;
use App\Enums\WalletTransactionStatus;
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

            $todayOrders = Order::whereDate('created_at', $now)->count();
            $todaySales = OrderDetail::whereDate('created_at', $now)->sum('price');

            // مجموع فروش ناخالص ماه (ارزش دفتری محصولات)
            $monthSales = OrderDetail::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->sum('price');

            // 🟢 اصلاح طلایی ۱: سود خالص ماهانه سایت (کارمزد ناخالص منهای سوبسید نقدی همان ماه)
            $monthStats = OrderDetail::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->selectRaw('SUM(site_share) as gross_share, SUM(platform_subsidy) as subsidy')
                ->first();
            $siteIncomeMonth = ($monthStats->gross_share ?? 0) - ($monthStats->subsidy ?? 0);

            // 🟢 اصلاح طلایی ۲: کل سود خالص تاریخی سایت از ابتدا تاکنون
            $totalStats = OrderDetail::selectRaw('SUM(site_share) as gross_share, SUM(platform_subsidy) as subsidy')
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
            return [
                'latest_settlements' => SellerSettlement::with(['seller.user']) // 🟢 لود اتمیک ریلیشن‌ها برای جلوگیری از N+1 Query
                ->latest()
                    ->take(10)
                    ->get(),

                'latest_orders' => Order::with('user')
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

            $rows = OrderDetail::selectRaw('
                    MONTH(created_at) as month,
                    SUM(price) as total
                ')
                ->whereYear('created_at', $year)
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

    // 🟢 لود بهینه لایه تحلیل روزانه و کش اختصاصی آن
    public function insights(): array
    {
        return Cache::remember('admin.panel.insights', 60, function () {
            $today = now();
            $yesterday = now()->subDay();

            $todaySales = OrderDetail::whereDate('created_at', $today)->sum('price');
            $yesterdaySales = OrderDetail::whereDate('created_at', $yesterday)->sum('price');

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
            'insights' => $this->insights(), // 🟢 به خروجی نهایی اضافه شد
        ];
    }

    public function clearCache(): void
    {
        Cache::forget('admin.panel.kpis');
        Cache::forget('admin.panel.sellers');
        Cache::forget('admin.panel.latest');
        Cache::forget('admin.panel.monthly_sales');
        Cache::forget('admin.panel.insights'); // 🟢 اضافه شد
    }
}
