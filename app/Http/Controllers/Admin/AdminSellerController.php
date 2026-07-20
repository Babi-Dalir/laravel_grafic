<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WalletTransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerVerificationRequest;
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
        // 🔒 ۱. چک کردن خالی بودن مسیر در دیتابیس
        if (empty($request->resume)) {
            session()->flash('message', 'فایل رزومه‌ای برای این درخواست ثبت نشده است.');
            return redirect()->back();
        }

        // 🔒 ۲. بررسی وجود فیزیکی فایل روی دیسک files (یا public بر اساس کانفیگ شما)
        $disk = Storage::disk('files');

        if (!$disk->exists($request->resume)) {
            session()->flash('message', 'فایل رزومه مورد نظر پیدا نشد یا از روی سرور پاک شده است.');
            return redirect()->back();
        }

        // 🟢 ۳. دانلود امن و بی‌دردسر فایل
        return response()->download($disk->path($request->resume));
    }

    public function sellerList()
    {
        $title = "لیست فروشندگان";
        return view('admin.sellers.list', compact('title'));
    }

    // ۱. نمایش فرم ویرایش اطلاعات فروشنده توسط ادمین
    public function editSellerByAdmin($id)
    {
        $title = 'ویرایش اطلاعات فروشنده توسط مدیریت';
        $seller = Seller::query()->findOrFail($id);

        return view('admin.sellers.edit', compact('title', 'seller'));
    }

// ۲. به‌روزرسانی اطلاعات فروشنده توسط ادمین

    public function updateSellerByAdmin(SellerVerificationRequest $request, $id)
    {
        $seller = Seller::query()->findOrFail($id);

        $seller->update([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'brand_name'     => $request->brand_name,
            'national_code'  => $request->national_code,
            'card_number'    => $request->card_number,
            'account_number' => $request->account_number,
            'iban'           => $request->iban, // توسط prepareForValidation کاملاً استاندارد شده است
        ]);

        return redirect()
            ->route('seller.list')
            ->with('message', 'اطلاعات فروشنده با موفقیت ویرایش شد.');
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

        // 🟢 انوم وضعیت سفارش موفق جهت گاردگذاری محاسبات
        $payedStatus = \App\Enums\OrderStatus::Payed->value;

        // ۲. تجمیع هوشمند آمار با اصطلاحات دقیق حسابداری (فقط سفارشات پرداخت موفق)
        $orderStats = OrderDetail::query()
            ->where('seller_id', $sellerUserId)
            ->whereHas('order', function ($q) use ($payedStatus) {
                $q->where('status', $payedStatus);
            })
            ->selectRaw('
            SUM(price) as total_product_value,
            SUM(seller_share) as total_seller_income,
            SUM(site_share) as total_gross_site_income,
            SUM(platform_subsidy) as total_platform_subsidy,
            COUNT(DISTINCT order_id) as total_orders
        ')
            ->first();

        // تغییر نام مفهوم درآمد به سود خالص پلتفرم از این فروشنده
        $netPlatformProfit = ($orderStats->total_gross_site_income ?? 0) - ($orderStats->total_platform_subsidy ?? 0);

        // ۳. تجمیع آمار تراکنش‌های کیف پول (این بخش خودکار درست است چون تراکنش‌ها زمان پرداخت موفق متولد می‌شوند)
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

        // 🟢 پچ جدول لیست فروش‌ها: فقط اقلامی که فاکتور مادر آنها با موفقیت پرداخت شده است
        $sales = OrderDetail::query()
            ->with(['product:id,name', 'order:id,order_code'])
            ->where('seller_id', $sellerUserId)
            ->whereHas('order', function ($q) use ($payedStatus) {
                $q->where('status', $payedStatus);
            })
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
            'netPlatformProfit'   => $netPlatformProfit,
            'totalProducts'       => $totalProducts,
            'pendingBalance'      => $walletStats->pending_balance ?? 0,
            'settledBalance'      => $walletStats->settled_balance ?? 0,
            'transactions'        => $transactions,
            'settlements'         => $settlements,
            'sales'               => $sales
        ]);
    }
}
