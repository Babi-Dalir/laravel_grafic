<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $fillable = [
        'mobile',
        'email',
        'code',

    ];

    public static function canSendCode($entry)
    {
        return ! self::query()
            ->where(function ($query) use ($entry) {
                $query->where('mobile', $entry)
                    ->orWhere('email', $entry);
            })
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();
    }

    //ساخت کد جدید
    public static function createVerificationCode($entry,$code)
    {
        self::query()
            ->where('mobile',$entry)
            ->orWhere('email', $entry)
            ->delete();

        if (filter_var($entry, FILTER_VALIDATE_EMAIL)){
            self::query()->create([
                'email'=>$entry,
                'code'=>$code
            ]);
        }else {
            self::query()->create([
                'mobile'=>$entry,
                'code'=>$code
            ]);
        }
    }

    //بررسی اعتبار کد
    public static function checkVerificationCode($entry,$code)
    {
        return self::query()
            ->where(function ($query) use ($entry) {
                $query->where('mobile', $entry)
                    ->orWhere('email', $entry);
            })
            ->where('code', $code)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

    }
}
