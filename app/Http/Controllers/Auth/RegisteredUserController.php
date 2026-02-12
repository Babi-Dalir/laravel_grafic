<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'lowercase', 'max:11', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Session::put('name',$request->name);
        Session::put('mobile',$request->mobile);
        Session::put('password',$request->password);

        $check_send_sms = VerificationCode::checkTimeCode($request->mobile);
        if ($check_send_sms){
            $code = rand('11111', '99999');
            VerificationCode::createVerificationCode($request->mobile,$code);

            //send sms
            $serviceSMS = new ServiceSMS();
            $serviceSMS->setReciever($request->mobile);
            $serviceSMS->setContent($code);

            $messageService = new MessageService($serviceSMS);
            $messageService->send();
        }else{
            return redirect()->back()->with('message','برای ارسال مجدد کد تایید 2 دقیقه صبر کنید');
        }
        return redirect()->route('verify.mobile');
    }
}
