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

        $title = 'جزئیات فروشنده';

        $seller = Seller::query()->with('user')->find($seller_id);

        $totalSalesAmount = OrderDetail::query()
            ->where('seller_id', $seller->user_id)
            ->sum('price');

        $totalSellerIncome = OrderDetail::query()
            ->where('seller_id', $seller->user_id)
            ->sum('seller_share');

        $totalOrders = OrderDetail::query()
            ->where('seller_id', $seller->user_id)
            ->distinct()
            ->count('order_id');

        $totalProducts = Product::query()
            ->where('user_id', $seller->user_id)
            ->count();

        $pendingBalance = SellerWalletTransaction::query()
            ->where('seller_id', $seller->id)
            ->where('status', WalletTransactionStatus::Pending->value)
            ->sum('amount');

        $settledBalance = SellerWalletTransaction::query()
            ->where('seller_id', $seller->id)
            ->where('status', WalletTransactionStatus::Settled->value)
            ->sum('amount');

        $transactions = SellerWalletTransaction::query()
            ->with('order')
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(10)
            ->get();

        $settlements = SellerSettlement::query()
            ->with('user')
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(10, ['*'], 'settlements_page');

        $sales = OrderDetail::query()
            ->with(['product', 'order'])
            ->where('seller_id', $seller->user_id)
            ->latest()
            ->paginate(10, ['*'], 'sales_page');

        return view('admin.sellers.seller_detail', compact(
            'title',
            'seller',
            'totalSalesAmount',
            'totalSellerIncome',
            'totalOrders',
            'totalProducts',
            'pendingBalance',
            'settledBalance',
            'transactions',
            'settlements',
            'sales'
        ));
    }
}
