<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\SellerRequestStatus;
use App\Helpers\FileManager;
use App\Helpers\ImageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSellerRequest;
use App\Http\Requests\ProfileUpdate;
use App\Models\Comment;
use App\Models\Downloads;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        // پیشنهاد لحظه ای

        $instant_offers = Product::smartOffer()
            ->get();
        return view('frontend.profile.profile', compact('user','instant_offers'));
    }


    public function profileUpdate(ProfileUpdate $request)
    {
        $user = auth()->user();

        // 🟢 ۱. ذخیره نام عکس قدیمی دقیقاً همین‌جا (قبل از هرگونه آپدیت)
        $oldImage = $user->image;

        $imageName = $oldImage; // مقدار پیش‌فرض تصویر، همان تصویر قبلی است
        $newImageSaved = false;

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $imageName = ImageManager::saveImage('users', $request->image);
                $newImageSaved = true;
            }

            // ۲. بروزرسانی اطلاعات اصلی کاربر
            $user->update([
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
                'mobile'    => $user->mobile ?? $request->input('mobile'),
                'email'     => $user->email ?? $request->input('email'),
                'image'     => $imageName
            ]);

            // ۳. بروزرسانی یا ایجاد اطلاعات پروفایل فرعی
            $user->userProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'telegram'  => $request->input('telegram'),
                    'eta'       => $request->input('eta'),
                    'instagram' => $request->input('instagram'),
                    'website'   => $request->input('website'),
                    'bio'       => $request->input('bio'),
                ]
            );

            DB::commit();

            // 🟢 ۴. همگام‌سازی آنی سشن با اطلاعات جدید برای سایدبار
            auth()->setUser($user);

            // 🟢 ۵. حذف عکس قدیمی واقعی بدون آسیب زدن به عکس جدید
            if ($newImageSaved && $oldImage) {
                ImageManager::unlinkImage(
                    'users', (object)['image' => $oldImage]
                );
            }

            return redirect()->back()->with('message', "اطلاعات شما با موفقیت ثبت شد");

        } catch (Exception $e) {
            DB::rollBack();

            if ($newImageSaved && isset($imageName)) {
                ImageManager::unlinkImage('users', (object)['image' => $imageName]);
            }

            report($e);
            return redirect()->back()->with('error', "خطایی در ثبت اطلاعات رخ داد. لطفاً مجدداً تلاش کنید.");
        }
    }

    public function profileOrders()
    {
        // پیشنهاد لحظه ای

        $instant_offers = Product::smartOffer()
            ->get();

        return view('frontend.profile.profile_orders',compact('instant_offers'));
    }

    public function profileOrdersDetails($order_id)
    {
        // پیشنهاد لحظه ای

        $instant_offers = Product::smartOffer()
            ->get();

        return view('frontend.profile.profile_order_details',compact('order_id','instant_offers'));
    }

    public function profileFavorites()
    {
        // پیشنهاد لحظه ای

        $instant_offers = Product::smartOffer()
            ->get();

        return view('frontend.profile.profile_favorites',compact('instant_offers'));
    }

//    public function profileComments()
//    {
//        $user = auth()->user();
//        $comments = Comment::query()->where('user_id', $user->id)->get();
//        return view('frontend.profile.profile_comments', compact('comments'));
//    }
    public function profileDownloads()
    {
        return view('frontend.profile.profile_downloads');
    }

    public function profileRequestSeller()
    {
        $user = auth()->user();
        $sellerRequest = SellerRequest::query()
            ->where('user_id', $user->id)
            ->first();

        return view('frontend.profile.profile_request_seller', compact('user','sellerRequest'));
    }

    public function profileStoreRequestSeller(ProfileSellerRequest $request)
    {
        $user = auth()->user();
        $sellerRequest = $user->sellerRequest;

        // ۱. اگر کاربر از قبل درخواستی دارد که تایید شده یا در حال بررسی است، اجازه ثبت مجدد ندهید
        if ($sellerRequest) {
            if ($sellerRequest->status === SellerRequestStatus::Approved->value) {
                return back()->with('error', 'حساب کاربری شما قبلاً به عنوان طراح تایید شده است.');
            }

            if ($sellerRequest->status === SellerRequestStatus::Pending->value) {
                return back()->with('error', 'درخواست شما در حال بررسی است و امکان ارسال مجدد وجود ندارد.');
            }
        }

        // ۲. اگر درخواستی از قبل نبود یا درخواست قبلی رد شده بود (Rejected)، اجازه بروزرسانی و ارسال مجدد بدهید
        if ($sellerRequest && $sellerRequest->status === SellerRequestStatus::Rejected->value) {

            // اینجا به جای ساخت رکورد جدید، رکورد قبلی را بروزرسانی کرده و وضعیت را ریست می‌کنیم
            $sellerRequest->update([
                'brand_name'  => $request->input('brand_name'),
                'portfolio'   => $request->input('portfolio'),
                'reason'      => $request->input('reason'),
                'status'      => SellerRequestStatus::Pending->value, // 🟢 تغییر وضعیت مجدد به در حال بررسی
                'admin_note'  => null, // پاک کردن دلیل رد قبلی
                'reviewed_at' => null, // پاک کردن تاریخ بررسی قبلی
            ]);

            // مدیریت فایل رزومه جدید در صورت آپلود
            if ($request->hasFile('resume')) {
                // در صورت نیاز فیلد رزومه قبلی را حذف کنید و رزومه جدید را ذخیره کنید
                $resumePath = FileManager::saveFile('resumes', $request->resume);
                $sellerRequest->update(['resume' => $resumePath]);
            }

            return back()->with('message', 'درخواست اصلاح‌شده شما با موفقیت ثبت شد و مجدداً در صف بررسی قرار گرفت.');
        }

        // ۳. اگر اولین بار است که درخواست می‌دهد:
        SellerRequest::createSellerRequest($request);

        return back()->with('message', 'درخواست همکاری شما با موفقیت ثبت شد.');
    }

    public function profileVerificationSeller()
    {
        $user = auth()->user();

        // گرفتن اطلاعات فروشنده مربوط به کاربر
        $seller = Seller::query()->where('user_id', $user->id)->first();

        // اگر نبود، یک instance خالی برای جلوگیری از error در view
        if (!$seller) {
            $seller = new Seller();
        }

        return view('frontend.profile.profile_verification_seller', compact('seller'));
    }
}
