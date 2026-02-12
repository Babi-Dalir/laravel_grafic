<?php

namespace App\Http\Controllers\Seller;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Guaranty;
use App\Models\Seller;
use App\Models\Tag;
use App\Models\UserTransaction;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function sellerProductList()
    {
        $title = "لیست محصولات فروشنده";
        return view('seller.seller_products.list', compact('title'));
    }

    public function createSellerProduct()
    {
        $title = "ایجاد محصول توسط فروشنده";
        $categories = Category::getCategories();
        $brands = Brand::query()->pluck('name','id');
        $tags = Tag::query()->pluck('name','id');
        $guaranties = Guaranty::query()->pluck('name','id');
        $colors = Color::query()->pluck('name','id');
        return view('admin.sellers.create',compact('title','categories','brands','tags','guaranties','colors'));
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
