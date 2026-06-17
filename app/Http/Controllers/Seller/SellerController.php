<?php

namespace App\Http\Controllers\Seller;

use App\Enums\SellerRequestStatus;
use App\Enums\SellerStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\SellerProductRequest;
use App\Http\Requests\SellerVerificationRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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
    public function storeSellerProduct(SellerProductRequest $request)
    {
        $product = Product::createProduct($request);
        return redirect()
            ->route('add.product.gallery', $product->id)
            ->with('message', 'محصول ایجاد شد. حالا تصاویر گالری را ثبت کنید.');
    }

    public function editSellerProduct(string $id)
    {
        $title ="ویرایش محصول";
        $categories = Category::getCategories();
        $tags = Tag::query()->pluck('name','id');
        $product = Product::findOrfail($id);
        return view('seller.seller_products.edit',compact('title','categories','tags','product'));
    }

    public function updateSellerProduct(SellerProductRequest $request, string $id)
    {
        Product::updateProduct($request,$id);
        return redirect()->route('seller.product.list')->with('message', 'محصول با موفقیت ویرایش شد');
    }

    public function sellerTransactionList()
    {
        $title = "لیست تراکنشهای فروشنده";
        return view('seller.seller_transactions.list', compact('title'));
    }

    public function createSellerVerification()
    {
        $title = 'احراز هویت';

        $seller = auth()->user()->seller;

        return view(
            'seller.seller_verifications.create',
            compact('title', 'seller')
        );
    }
    public function storeSellerVerification(SellerVerificationRequest $request)
    {
        $seller = auth()->user()->seller;

        $seller->update([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'brand_name'     => $request->brand_name,
            'national_code'  => $request->national_code,
            'card_number'    => $request->card_number,
            'account_number' => $request->account_number,
            'iban'           => strtoupper($request->iban),

            // ارسال برای بررسی ادمین
            'status' => SellerStatus::Pending->value,
        ]);

        return redirect()
            ->back()
            ->with('success', 'اطلاعات احراز هویت با موفقیت ثبت شد و در انتظار بررسی است.');
    }
}
