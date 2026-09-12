<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\Message\SMS\ServiceMelipayamak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    /**
     * فرم درخواست OTP
     */
    public function showRequestForm()
    {
        return view('frontend.auth.forgot_password');
    }

    /**
     * ارسال OTP برای فراموشی رمز
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'regex:/^09[0-9]{9}$/'],
        ], [
            'mobile.required' => 'لطفاً شماره موبایل را وارد کنید.',
            'mobile.regex' => 'فرمت شماره موبایل معتبر نیست.',
        ]);

        $mobile = $request->input('mobile');

        /*
         * Rate limit by mobile + IP.
         */
        $rateLimitKey = 'forgot-password:' . $mobile . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return back()
                ->withErrors([
                    'mobile' => 'تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.',
                ])
                ->withInput();
        }

        /*
         * Only existing users can request password reset.
         */
        if (! User::query()->where('mobile', $mobile)->exists()) {
            return back()
                ->withErrors([
                    'mobile' => 'کاربری با این شماره موبایل یافت نشد.',
                ])
                ->withInput();
        }

        /*
         * Minimum 2-minute interval.
         */
        if (! VerificationCode::canSendCode($mobile)) {
            return back()
                ->withErrors([
                    'mobile' => 'برای ارسال مجدد کد باید ۲ دقیقه صبر کنید.',
                ])
                ->withInput();
        }

        RateLimiter::hit($rateLimitKey, 120);

        /*
         * Generate + send OTP by Melipayamak.
         */
        $melipayamak = new ServiceMelipayamak();

        $code = $melipayamak->sendOTP($mobile);

        if ($code === null) {
            return back()
                ->withErrors([
                    'mobile' => 'ارسال کد تایید با مشکل مواجه شد. لطفاً دوباره تلاش کنید.',
                ])
                ->withInput();
        }

        /*
         * Store hashed OTP.
         */
        VerificationCode::createOtp(
            $mobile,
            $code
        );

        /*
         * Store reset flow data.
         */
        Session::put('reset_mobile', $mobile);

        Session::forget('reset_verified');

        return redirect()
            ->route('password.verify.form.otp');
    }

    /**
     * ارسال مجدد OTP
     */
    public function resendOtp(Request $request)
    {
        $mobile = Session::get('reset_mobile');

        if (! $mobile) {
            return response()->json([
                'status' => 'error',
                'message' => 'اطلاعات شماره موبایل یافت نشد.',
            ], 422);
        }

        /*
         * Make sure the user still exists.
         */
        if (! User::query()->where('mobile', $mobile)->exists()) {
            Session::forget([
                'reset_mobile',
                'reset_verified',
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'کاربر یافت نشد.',
            ], 422);
        }

        /*
         * Rate limit.
         */
        $rateLimitKey =
            'forgot-password-resend:' .
            $mobile .
            '|' .
            $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.',
            ], 429);
        }

        /*
         * Minimum 2-minute interval.
         */
        if (! VerificationCode::canSendCode($mobile)) {
            return response()->json([
                'status' => 'error',
                'message' => 'لطفاً ۲ دقیقه صبر کرده و مجدداً تلاش کنید.',
            ], 429);
        }

        RateLimiter::hit($rateLimitKey, 120);

        /*
         * Send new OTP.
         */
        $melipayamak = new ServiceMelipayamak();

        $code = $melipayamak->sendOTP($mobile);

        if ($code === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'ارسال کد تایید با مشکل مواجه شد.',
            ], 500);
        }

        VerificationCode::createOtp(
            $mobile,
            $code
        );

        return response()->json([
            'status' => 'success',
            'message' => 'کد جدید ارسال شد.',
        ]);
    }

    /**
     * فرم تایید OTP
     */
    public function showVerifyForm()
    {
        if (! Session::has('reset_mobile')) {
            return redirect()
                ->route('password.request.otp');
        }

        return view('frontend.auth.verify_forgot_otp');
    }

    /**
     * تایید OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'array', 'size:5'],
            'code.*' => ['required', 'digits:1'],
        ], [
            'code.required' => 'لطفاً کد تایید را وارد کنید.',
            'code.size' => 'کد تایید باید ۵ رقمی باشد.',
        ]);

        $mobile = Session::get('reset_mobile');

        if (! $mobile) {
            return redirect()
                ->route('password.request.otp')
                ->with(
                    'error',
                    'نشست شما منقضی شده است.'
                );
        }

        $code = implode('', $request->input('code'));

        /*
         * Verify OTP.
         */
        if (! VerificationCode::verifyOtp($mobile, $code)) {
            return back()
                ->with(
                    'error',
                    'کد وارد شده صحیح نمی‌باشد یا منقضی شده است.'
                );
        }

        /*
         * Mark reset flow as verified.
         */
        Session::put('reset_verified', true);

        return redirect()
            ->route('password.reset.form.otp');
    }

    /**
     * فرم تعیین رمز جدید
     */
    public function showResetForm()
    {
        if (
            ! Session::has('reset_mobile') ||
            ! Session::get('reset_verified')
        ) {
            return redirect()
                ->route('password.request.otp');
        }

        return view('frontend.auth.reset_password');
    }

    /**
     * تغییر رمز
     */
    public function resetPassword(Request $request)
    {
        if (
            ! Session::has('reset_mobile') ||
            ! Session::get('reset_verified')
        ) {
            return redirect()
                ->route('password.request.otp');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'password.required' => 'لطفاً رمز عبور جدید را وارد کنید.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.',
        ]);

        $mobile = Session::get('reset_mobile');

        $user = User::query()
            ->where('mobile', $mobile)
            ->first();

        if (! $user) {
            Session::forget([
                'reset_mobile',
                'reset_verified',
            ]);

            return redirect()
                ->route('password.request.otp')
                ->with(
                    'error',
                    'کاربر یافت نشد.'
                );
        }

        /*
         * Change password.
         */
        $user->update([
            'password' => Hash::make(
                $request->input('password')
            ),
        ]);

        /*
         * Delete OTP.
         */
        VerificationCode::query()
            ->where('mobile', $mobile)
            ->delete();

        /*
         * Destroy reset session.
         */
        Session::forget([
            'reset_mobile',
            'reset_verified',
        ]);

        return redirect()
            ->route('login')
            ->with(
                'success',
                'رمز عبور شما با موفقیت تغییر یافت. اکنون می‌توانید وارد شوید.'
            );
    }
}
