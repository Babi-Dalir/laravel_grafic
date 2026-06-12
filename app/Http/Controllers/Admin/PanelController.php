<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderDetailStatus;
use App\Enums\OrderStatus;
use App\Enums\SellerStatus;
use App\Enums\SettlementStatus;
use App\Enums\WalletTransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Seller;
use App\Models\SellerSettlement;
use App\Models\SellerWalletTransaction;
use App\Services\Admin\PanelDashboardService;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function __construct(
        private PanelDashboardService $service
    ) {}

    public function index()
    {
        $data = $this->service->buildDashboard();

        $todayOrders = Order::query()
            ->whereDate('created_at', today())
            ->count();

        $todaySales = OrderDetail::query()
            ->where('status',OrderDetailStatus::Paid->value)
            ->whereDate('created_at', today())
            ->sum('price');

        $monthSales = OrderDetail::query()
            ->where('status',OrderDetailStatus::Paid->value)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('price');

        $activeSellers = Seller::query()
            ->where('status', SellerStatus::Active->value)
            ->count();

        $pendingBalance = SellerWalletTransaction::query()
            ->where('status', WalletTransactionStatus::Pending->value)
            ->sum('amount');

        $settledBalance = SellerWalletTransaction::query()
            ->where('status', WalletTransactionStatus::Settled->value)
            ->sum('amount');

        $siteIncomeMonth = OrderDetail::query()
            ->where('status',OrderDetailStatus::Paid->value)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('site_share');

        $totalSiteIncome = OrderDetail::query()
            ->where('status',OrderDetailStatus::Paid->value)
            ->sum('site_share');

        $latestSettlements = SellerSettlement::query()
            ->with(['seller.user:id,name', 'user:id,name'])
            ->where('status', SettlementStatus::Paid->value)
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get();

        $latestOrders = Order::query()
            ->with('user')
            ->where('status', OrderStatus::Payed->value)
            ->latest()
            ->take(10)
            ->get();

        $monthlySales = OrderDetail::query()
            ->selectRaw('MONTH(created_at) as month, SUM(price) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('admin.index', $data, compact(
            'todayOrders',
            'todaySales',
            'monthSales',
            'activeSellers',
            'pendingBalance',
            'settledBalance',
            'siteIncomeMonth',
            'totalSiteIncome',
            'latestSettlements',
            'latestOrders',
            'monthlySales',
        ));
    }

}
