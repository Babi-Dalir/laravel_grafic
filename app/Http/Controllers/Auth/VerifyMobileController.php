<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class VerifyMobileController extends Controller
{
    public function verifyMobile()
    {
        return view('frontend.auth.verify_mobile');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'array', 'size:5'],
            'code.*' => ['required', 'numeric']
        ]);
        
        $code = (int)implode('', $request->code);
        $mobile = Session::get('mobile');
        $check = VerificationCode::checkVerificationCode($mobile, $code);
        if (!$check) {

            return redirect()
                ->back()
                ->with(
                    'message',
                    'کد وارد شده صحیح نمیباشد'
                );
        }

        $check_user = User::query()->where('mobile', $mobile)->exists();
        if ($check_user) {

            return redirect()
                ->route('login')
                ->with(
                    'message',
                    'این شماره قبلا ثبت شده است'
                );
        }

        $user = User::create([
            'name' => Session::get('name'),
            'mobile' => Session::get('mobile'),

            // قبلا هش شده
            'password' => Session::get('password'),
        ]);

        VerificationCode::query()
            ->where('mobile', $mobile)
            ->delete();

        Session::forget([
            'name',
            'mobile',
            'password',
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');

    }
}
