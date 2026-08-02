<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\Message\MessageService;
use App\Services\Message\SMS\ServiceSMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    // ۱. فرم ورود شماره
    public function showRequestForm()
    {
        return view('frontend.auth.forgot_password');
    }

    // ۲. بررسی شماره و ارسال پیامک OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'regex:/^09[0-9]{9}$/'],
        ], [
            'mobile.required' => 'لطفاً شماره موبایل را وارد کنید.',
            'mobile.regex' => 'فرمت شماره موبایل معتبر نیست.',
        ]);

        $mobile = $request->mobile;

        // بررسی وجود شماره در دیتابیس
        if (!User::where('mobile', $mobile)->exists()) {
            return back()->withErrors(['mobile' => 'کاربری با این شماره موبایل یافت نشد.'])->withInput();
        }

        // چک کردن زمان ارسال (جلوگیری از اسپم)
        if (!VerificationCode::canSendCode($mobile)) {
            return back()->withErrors(['mobile' => 'برای ارسال مجدد کد باید ۲ دقیقه صبر کنید.'])->withInput();
        }

        $code = random_int(10000, 99999);
        VerificationCode::createVerificationCode($mobile, $code);

        // ارسال SMS با ملی پیامک
        $serviceSMS = new ServiceSMS($mobile, $code);
        (new MessageService($serviceSMS))->send();

        Session::put('reset_mobile', $mobile);

        return redirect()->route('password.verify.form.otp');
    }

    // ۳. ارسال مجدد کد آژاکسی
    public function resendOtp()
    {
        $mobile = Session::get('reset_mobile');

        if (!$mobile) {
            return response()->json(['status' => 'error', 'message' => 'اطلاعات شماره همراه یافت نشد.'], 422);
        }

        if (!VerificationCode::canSendCode($mobile)) {
            return response()->json(['status' => 'error', 'message' => 'لطفاً ۲ دقیقه صبر کرده و مجدداً تلاش کنید.'], 422);
        }

        $newCode = random_int(10000, 99999);
        VerificationCode::createVerificationCode($mobile, $newCode);

        $serviceSMS = new ServiceSMS($mobile, $newCode);
        (new MessageService($serviceSMS))->send();

        return response()->json(['status' => 'success', 'message' => 'کد جدید ارسال شد.']);
    }

    // ۴. فرم تایید کد OTP
    public function showVerifyForm()
    {
        if (!Session::has('reset_mobile')) {
            return redirect()->route('password.request.otp');
        }

        return view('frontend.auth.verify_forgot_otp');
    }

    // ۵. بررسی کد OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'array', 'size:5'],
            'code.*' => ['required', 'numeric'],
        ]);

        $mobile = Session::get('reset_mobile');
        if (!$mobile) {
            return redirect()->route('password.request.otp')->with('error', 'نشست شما منقضی شده است.');
        }

        $code = (int) implode('', $request->code);

        if (!VerificationCode::checkVerificationCode($mobile, $code)) {
            return back()->with('error', 'کد وارد شده صحیح نمی‌باشد یا منقضی شده است.');
        }

        Session::put('reset_verified', true);

        return redirect()->route('password.reset.form.otp');
    }

    // ۶. فرم ثبت رمز جدید
    public function showResetForm()
    {
        if (!Session::has('reset_mobile') || !Session::get('reset_verified')) {
            return redirect()->route('password.request.otp');
        }

        return view('frontend.auth.reset_password');
    }

    public function resetPassword(Request $request)
    {
        if (!Session::has('reset_mobile') || !Session::get('reset_verified')) {
            return redirect()->route('password.request.otp');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'لطفاً رمز عبور جدید را وارد کنید.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.',
        ]);

        $mobile = Session::get('reset_mobile');

        // ۱. به‌روزرسانی رمز عبور
        User::where('mobile', $mobile)->update([
            'password' => Hash::make($request->password),
        ]);

        // ۲. پاک‌سازی کامل دیتابیس و سشن‌ها
        VerificationCode::query()->where('mobile', $mobile)->delete();
        Session::forget(['reset_mobile', 'reset_verified']);

        // ۳. هدایت به لاگین
        return redirect()->route('login')
            ->with('success', 'رمز عبور شما با موفقیت تغییر یافت. اکنون می‌توانید وارد شوید.');
    }
}
