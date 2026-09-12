<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\Message\SMS\ServiceMelipayamak;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerifyMobileController extends Controller
{
    /**
     * نمایش صفحه تایید شماره
     */
    public function verifyMobile()
    {
        if (! Session::has('register.mobile')) {
            return redirect()
                ->route('register')
                ->with('message', 'اطلاعات ثبت‌نام یافت نشد.');
        }

        return view('frontend.auth.verify_mobile');
    }

    /**
     * تایید OTP ثبت نام
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'array', 'size:5'],
            'code.*' => ['required', 'digits:1'],
        ], [
            'code.required' => 'لطفاً کد تایید را وارد کنید.',
            'code.size' => 'کد تایید باید ۵ رقمی باشد.',
        ]);

        $mobile = Session::get('register.mobile');

        if (! $mobile) {
            return redirect()
                ->route('register')
                ->with('error', 'نشست ثبت‌نام شما منقضی شده است.');
        }

        /*
         * Make sure this mobile number has not been registered
         * while the OTP flow was in progress.
         */
        if (User::query()->where('mobile', $mobile)->exists()) {
            Session::forget([
                'register.name',
                'register.mobile',
                'register.password',
            ]);

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'این شماره قبلاً ثبت شده است.'
                );
        }

        /*
         * Convert OTP array to string.
         * Keep it as string to preserve leading zeros.
         */
        $code = implode('', $request->input('code'));

        /*
         * Verify hashed OTP.
         */
        if (! VerificationCode::verifyOtp($mobile, $code)) {
            return back()
                ->with(
                    'error',
                    'کد وارد شده صحیح نمی‌باشد یا منقضی شده است.'
                );
        }

        /*
         * Get pending registration data.
         */
        $name = Session::get('register.name');
        $password = Session::get('register.password');

        if (! $name || ! $password) {
            Session::forget([
                'register.name',
                'register.mobile',
                'register.password',
            ]);

            return redirect()
                ->route('register')
                ->with(
                    'error',
                    'اطلاعات ثبت‌نام شما منقضی شده است.'
                );
        }

        /*
         * Create user.
         */
        $user = User::create([
            'name' => $name,
            'mobile' => $mobile,
            'password' => $password,
        ]);

        /*
         * Remove used OTP records.
         */
        VerificationCode::query()
            ->where('mobile', $mobile)
            ->delete();

        /*
         * Remove pending registration data.
         */
        Session::forget([
            'register.name',
            'register.mobile',
            'register.password',
        ]);

        /*
         * Fire Registered event.
         */
        event(new Registered($user));

        /*
         * Login user.
         */
        Auth::login($user);

        /*
         * Prevent session fixation.
         */
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /**
     * ارسال مجدد OTP
     */
    public function resendOtp(Request $request)
    {
        $mobile = Session::get('register.mobile');

        if (! $mobile) {
            return response()->json([
                'status' => 'error',
                'message' => 'اطلاعات ثبت‌نام یافت نشد.',
            ], 422);
        }

        /*
         * Do not allow OTP for an already registered number.
         */
        if (User::query()->where('mobile', $mobile)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'این شماره قبلاً ثبت شده است.',
            ], 422);
        }

        /*
         * Server-side resend limitation.
         */
        if (! VerificationCode::canSendCode($mobile)) {
            return response()->json([
                'status' => 'error',
                'message' => 'برای ارسال مجدد کد تایید باید ۲ دقیقه صبر کنید.',
            ], 429);
        }

        /*
         * Ask Melipayamak to generate and send a new OTP.
         */
        $melipayamak = new ServiceMelipayamak();

        $code = $melipayamak->sendOTP($mobile);

        if ($code === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'ارسال کد تایید با مشکل مواجه شد.',
            ], 500);
        }

        /*
         * Store only the hashed OTP.
         */
        VerificationCode::createOtp(
            $mobile,
            $code
        );

        return response()->json([
            'status' => 'success',
            'message' => 'کد جدید ارسال شد.',
        ]);
    }
}
