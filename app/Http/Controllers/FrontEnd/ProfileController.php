<?php

namespace App\Http\Controllers\FrontEnd;

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
        $imageName = $user->image; // ۱. مقدار پیش‌فرض تصویر، همان تصویر قبلی است
        $newImageSaved = false;

        try {

            DB::beginTransaction();

            if ($request->hasFile('image')) {

                $imageName = ImageManager::saveImage('users', $request->image);
                $newImageSaved = true;
            }

            // ۴. بروزرسانی اطلاعات اصلی کاربر
            $user->update([
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
                'mobile'    => $user->mobile ?? $request->input('mobile'),
                'email'     => $user->email ?? $request->input('email'),
                'image'     => $imageName
            ]);

            // ۵. بروزرسانی یا ایجاد اطلاعات پروفایل فرعی
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

            // ۶. حذف عکس قبلی "تنها و تنها" پس از موفقیت کامل دیتابیس و ذخیره فایل جدید
            if ($newImageSaved && $user->getOriginal('image')) {
                // متد getOriginal تصویر قبلی را از کش مدل می‌آورد تا عکس جدید متولد شده پاک نشود!
                ImageManager::unlinkImage(
                    'users', (object)['image' => $user->getOriginal('image')]
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

        if ($user->sellerRequest) {
            return back()->with(
                'error',
                'شما قبلا درخواست ثبت کرده اید'
            );
        }

        SellerRequest::createSellerRequest($request);

        return back()->with(
            'message',
            'درخواست شما با موفقیت ثبت شد'
        );
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
