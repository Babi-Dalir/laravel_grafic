<?php

namespace App\Http\Controllers\FrontEnd;

use App\Helpers\FileManager;
use App\Helpers\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Downloads;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        return view('frontend.profile.profile', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        if ($request->hasFile('image')) {
            ImageManager::unlinkImage('users', $user); // حذف عکس قبلی
            $imageName = ImageManager::saveImage('users', $request->image);
        }
        $user->update([
            'name' => $request->input('name'),
            'user_name' => $request->input('user_name'),
            'mobile' => $user->mobile == null ? $request->input('mobile') : $user->mobile,
            'email' => $user->email == null ? $request->input('email') : $user->email,
            'image' => $request->image ? $imageName : $user->image
        ]);
        $user->userProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $request->input('phone'),
                'telegram' => $request->input('telegram'),
                'eta' => $request->input('eta'),
                'instagram' => $request->input('instagram'),
                'website' => $request->input('website'),
                'bio' => $request->input('bio'),
            ]
        );

        return redirect()->back()->with('message', "اطلاعات شما با موفقیت ثبت شد");
    }

    public function profileOrders()
    {
        return view('frontend.profile.profile_orders');
    }

    public function profileOrdersDetails($order_id)
    {
        return view('frontend.profile.profile_order_details',compact('order_id'));
    }

    public function profileFavorites()
    {
        return view('frontend.profile.profile_favorites');
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

    public function profileSeller()
    {
        $user = auth()->user();
        return view('frontend.profile.profile_seller', compact('user'));
    }

    public function profileStoreSeller(Request $request)
    {
        $user = auth()->user();
        $contract = FileManager::saveContract($request->contract, $request->input('company_name'));
        if ($user->seller) {
            $user->seller()->update([
                'company_name' => $request->input('company_name'),
                'company_economy_code' => $request->input('company_economy_code'),
                'contract' => $contract
            ]);
            return redirect()->back()->with('message', "اطلاعات شرکت شما با موفقیت ویرایش شد");
        } else {
            $user->seller()->create([
                'company_name' => $request->input('company_name'),
                'company_economy_code' => $request->input('company_economy_code'),
                'contract' => $contract
            ]);
            return redirect()->back()->with('message', "اطلاعات شرکت شما با موفقیت ثبت شد");
        }
    }
}
