<?php

namespace App\Http\Controllers\Seller;

use App\Enums\SellerRequestStatus;
use App\Enums\SellerStatus;
use App\Enums\TransactionType;
use App\Helpers\ImageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\SellerProductRequest;
use App\Http\Requests\SellerVerificationRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerRequest;
use App\Models\Tag;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $categories = Category::getLeafCategoriesInTree();
        $tags = Tag::query()->pluck('name','id');
        return view('seller.seller_products.create',compact('title','categories','tags'));
    }
    public function storeSellerProduct(SellerProductRequest $request)
    {
        $product = Product::createProduct($request);
        return redirect()
            ->route('add.seller.product.gallery', $product->id)
            ->with('message', 'محصول ایجاد شد. حالا تصاویر گالری را ثبت کنید.');
    }

    public function editSellerProduct(string $id)
    {
        $title ="ویرایش محصول";
        $categories = Category::getLeafCategoriesInTree();
        $tags = Tag::query()->pluck('name','id');
        $product = Product::findOrfail($id);
        return view('seller.seller_products.edit',compact('title','categories','tags','product'));
    }

    public function updateSellerProduct(SellerProductRequest $request, string $id)
    {
        Product::updateProduct($request,$id);
        return redirect()->route('seller.product.list')->with('message', 'محصول با موفقیت ویرایش شد');
    }

    public function addSellerProductGallery($id)
    {
        $product = Product::findOrFail($id);
        return view('seller.seller_products.add_seller_product_gallery',compact('product'));
    }

    public function storeSellerProductGallery(Request $request,$id)
    {
        $product = Product::findOrFail($id);

        // ۱. ولیدیشن دقیق با پیام‌های فارسی
        $validator = \Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048'
        ], [
            'file.required' => 'لطفاً یک فایل تصویر انتخاب کنید.',
            'file.image'    => 'فایل انتخاب شده باید تصویر باشد.',
            'file.mimes'    => 'فرمت‌های مجاز: jpeg, jpg, png, webp',
            'file.max'      => 'حجم تصویر پیش‌نمایش نمی‌تواند بیشتر از ۲ مگابایت باشد.',
        ]);

        // ۲. اگر ولیدیشن رد شد و درخواست AJAX بود (سمت Dropzone)
        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => $validator->errors()->first()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ۳. ذخیره‌سازی عکس
        Gallery::query()->create([
            'product_id' => $product->id,
            'image' => ImageManager::saveProductImage('products', $request->file('file')),
            'position' => Gallery::query()->where('product_id', $product->id)->count()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => 'تصویر با موفقیت اضافه شد.']);
        }

        return redirect()->back()->with('message', 'تصویر به گالری اضافه شد.');
    }

    public function createSellerProductProperty(Product $product)
    {
        return view('seller.seller_products.seller_product_properties',compact('product'));
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
