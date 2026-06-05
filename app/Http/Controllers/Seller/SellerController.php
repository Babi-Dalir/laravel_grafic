<?php

namespace App\Http\Controllers\Seller;

use App\Enums\SellerRequestStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Seller;
use App\Models\SellerRequest;
use App\Models\Tag;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{

    public function sellerRequestsList()
    {
        $title = "لیست درخواست ها";
        $requests = SellerRequest::with('user')
            ->latest()
            ->paginate(20);

        return view('seller.seller_requests.list',compact('title','requests'));
    }
    public function detailSellerRequest(SellerRequest $sellerRequest)
    {
        return view('admin.seller-requests.show',compact('sellerRequest')
        );
    }

    public function approveSellerRequest(SellerRequest $sellerRequest)
    {
        $sellerRequest->update([
            'status' => SellerRequestStatus::Approved->value,
            'reviewed_at' => now(),
        ]);

        $sellerRequest->user->assignRole('فروشنده');

        return back()->with(
            'message',
            'فروشنده با موفقیت تایید شد'
        );
    }
    public function rejectSellerRequest(Request $request,SellerRequest $sellerRequest)
    {
        $sellerRequest->update([
            'status' => SellerRequestStatus::Rejected->value,
            'admin_note' => $request->admin_note,
            'reviewed_at' => now(),
        ]);

        return back()->with(
            'message',
            'درخواست رد شد'
        );
    }

    public function downloadResume(SellerRequest $request)
    {
        $path = Storage::disk('files')->path($request->resume);
        return response()->download($path);
    }
    public function sellerProductList()
    {
        $title = "لیست محصولات فروشنده";
        return view('seller.seller_products.list', compact('title'));
    }

    public function createSellerProduct()
    {
        $title = "ایجاد محصول توسط فروشنده";
        $categories = Category::getCategories();
        $tags = Tag::query()->pluck('name','id');
        return view('admin.sellers.create',compact('title','categories','tags'));
    }
    public function storeSellerProduct(Request $request)
    {
        Seller::createSellerProduct($request);
        return redirect()->route('products.index')->with('message', 'محصول وارد شده توسط فروشنده با موفقیت ثبت شد');
    }

    public function sellerTransactionList()
    {
        $title = "لیست تراکنشهای فروشنده";
        $user_transactions = UserTransaction::query()
            ->where('user_id',auth()->user()->id)
            ->paginate(10);

        $deposit = UserTransaction::query()
            ->where('user_id',auth()->user()->id)
            ->where('type',TransactionType::Deposit->value)
            ->sum('money');

        $withdraw = UserTransaction::query()
            ->where('user_id',auth()->user()->id)
            ->where('type',TransactionType::Withdraw->value)
            ->sum('money');

        $total_money = $deposit - $withdraw;
        return view('seller.seller_transactions.list', compact('title','user_transactions','deposit','withdraw','total_money'));
    }
}
