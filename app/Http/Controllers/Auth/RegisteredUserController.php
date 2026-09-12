<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\Message\SMS\ServiceMelipayamak;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('frontend.auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $mobile = $request->validated('mobile');

        /*
         * Do not send OTP to an already registered number.
         */
        if (User::query()->where('mobile', $mobile)->exists()) {
            return back()
                ->withInput()
                ->with('message', 'این شماره قبلاً ثبت شده است.');
        }

        /*
         * Prevent repeated OTP requests.
         */
        if (! VerificationCode::canSendCode($mobile)) {
            return back()
                ->withInput()
                ->with(
                    'message',
                    'برای ارسال مجدد کد تأیید، لطفاً ۲ دقیقه صبر کنید.'
                );
        }

        $melipayamak = new ServiceMelipayamak();

        /*
         * Melipayamak generates and sends the OTP.
         */
        $code = $melipayamak->sendOTP($mobile);

        if ($code === null) {
            return back()
                ->withInput()
                ->with(
                    'message',
                    'ارسال کد تأیید با مشکل مواجه شد. لطفاً دوباره تلاش کنید.'
                );
        }

        /*
         * Store only the hashed OTP.
         */
        VerificationCode::createOtp(
            $mobile,
            $code
        );

        /*
         * Store pending registration data in the session.
         */
        Session::put([
            'register.name' => $request->validated('name'),
            'register.mobile' => $mobile,
            'register.password' => Hash::make(
                $request->validated('password')
            ),
        ]);

        return redirect()->route('verify.mobile');
    }
}
