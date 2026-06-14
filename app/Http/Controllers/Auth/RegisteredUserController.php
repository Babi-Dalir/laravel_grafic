<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\Message\MessageService;
use App\Services\Message\SMS\ServiceSMS;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('frontend.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        Session::put('name', $request->name);
        Session::put('mobile', $request->mobile);
        Session::put('password', Hash::make($request->password));

        $check_send_sms = VerificationCode::canSendCode($request->mobile);

        if (!$check_send_sms) {

            return redirect()
                ->back()
                ->with(
                    'message',
                    'برای ارسال مجدد کد تایید 2 دقیقه صبر کنید'
                );
        }

        $code = random_int(10000, 99999);

        VerificationCode::createVerificationCode(
            $request->mobile,
            $code
        );

        $serviceSMS = new ServiceSMS();
        $serviceSMS->setReciever($request->mobile);
        $serviceSMS->setContent($code);

        $messageService = new MessageService($serviceSMS);
        $messageService->send();

        return redirect()->route('verify.mobile');
    }
}
