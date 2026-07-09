<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WalletTransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerRequest;
use App\Models\SellerSettlement;
use App\Models\SellerWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSellerController extends Controller
{
    public function downloadResume(SellerRequest $request)
    {
        $path = Storage::disk('files')->path($request->resume);
        return response()->download($path);
    }

    public function sellerList()
    {
        $title = "لیست فروشندگان";
        return view('admin.sellers.list', compact('title'));
    }

    public function sellerRequestsList()
    {
        $title = "لیست درخواست‌های فروشندگی";

        return view('seller.seller_requests.list', compact('title'));
    }

    public function sellerSettlementList()
    {
        $title = "لیست تسویه حساب ها";
        return view('seller.seller_settlements.list', compact('title'));
    }

    public function adminSellerTransactionList()
    {
        $title = 'لیست تراکنش فروشندگان';

        return view('admin.transactions.list', compact('title'));
    }

    public function adminSellerDetail($seller_id)
    {
        $title = 'جزئیات جامع فروشنده';

        $seller = Seller::query()->with('user')->findOrFail($seller_id);
        $sellerUserId = $seller->user_id;

        // ۲. تجمیع هوشمند آمار با اصطلاحات دقیق حسابداری
        $orderStats = OrderDetail::query()
            ->where('seller_id', $sellerUserId)
            ->selectRaw('
            SUM(price) as total_product_value,
            SUM(seller_share) as total_seller_income,
            SUM(site_share) as total_gross_site_income,
            SUM(platform_subsidy) as total_platform_subsidy,
            COUNT(DISTINCT order_id) as total_orders
        ')
            ->first();

        // 🟢 اصلاح طلایی شما: تغییر نام مفهوم درآمد به سود خالص پلتفرم از این فروشنده
        $netPlatformProfit = ($orderStats->total_gross_site_income ?? 0) - ($orderStats->total_platform_subsidy ?? 0);

        // ۳. تجمیع آمار تراکنش‌های کیف پول
        $walletStats = SellerWalletTransaction::query()
            ->where('seller_id', $seller->id)
            ->selectRaw("
            SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as pending_balance,
            SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as settled_balance
        ", [WalletTransactionStatus::Pending->value, WalletTransactionStatus::Settled->value])
            ->first();

        $totalProducts = Product::query()->where('user_id', $sellerUserId)->count();

        $transactions = SellerWalletTransaction::query()
            ->with(['order:id,order_code'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(10)
            ->get();

        $settlements = SellerSettlement::query()
            ->with(['user:id,name'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(10, ['*'], 'settlements_page');

        $sales = OrderDetail::query()
            ->with(['product:id,name', 'order:id,order_code'])
            ->where('seller_id', $sellerUserId)
            ->latest()
            ->paginate(10, ['*'], 'sales_page');

        return view('admin.sellers.seller_detail', [
            'title'               => $title,
            'seller'              => $seller,
            'totalProductValue'   => $orderStats->total_product_value ?? 0,
            'totalSellerIncome'   => $orderStats->total_seller_income ?? 0,
            'totalOrders'         => $orderStats->total_orders ?? 0,
            'grossSiteIncome'     => $orderStats->total_gross_site_income ?? 0,
            'platformSubsidy'     => $orderStats->total_platform_subsidy ?? 0,
            'netPlatformProfit'   => $netPlatformProfit, // 🟢 متغیر جدید اصلاح‌شده
            'totalProducts'       => $totalProducts,
            'pendingBalance'      => $walletStats->pending_balance ?? 0,
            'settledBalance'      => $walletStats->settled_balance ?? 0,
            'transactions'        => $transactions,
            'settlements'         => $settlements,
            'sales'               => $sales
        ]);
    }
}
