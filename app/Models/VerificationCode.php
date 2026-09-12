<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VerificationCode extends Model
{
    protected $fillable = [
        'mobile',
        'email',
        'code',
        'expires_at',
        'attempts',
        'consumed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    /**
     * بررسی امکان ارسال OTP جدید
     */
    public static function canSendCode(string $entry): bool
    {
        return ! self::query()
            ->where(function ($query) use ($entry) {
                $query
                    ->where('mobile', $entry)
                    ->orWhere('email', $entry);
            })
            ->where(
                'created_at',
                '>=',
                now()->subMinutes(2)
            )
            ->exists();
    }

    /**
     * ایجاد OTP جدید
     */
    public static function createOtp(
        string $mobile,
        string $code
    ): self {
        return DB::transaction(function () use ($mobile, $code) {

            /*
             * Only the previous OTP for this mobile is removed.
             */
            self::query()
                ->where('mobile', $mobile)
                ->delete();

            return self::query()->create([
                'mobile' => $mobile,

                /*
                 * Never store the raw OTP.
                 */
                'code' => Hash::make($code),

                /*
                 * OTP validity: 5 minutes.
                 */
                'expires_at' => now()->addMinutes(5),

                'attempts' => 0,

                'consumed_at' => null,
            ]);
        });
    }

    /**
     * بررسی OTP
     */
    public static function verifyOtp(
        string $mobile,
        string $code
    ): bool {
        return DB::transaction(function () use ($mobile, $code) {

            /*
             * Lock the row to prevent concurrent verification.
             */
            $verification = self::query()
                ->where('mobile', $mobile)
                ->whereNull('consumed_at')
                ->where('expires_at', '>', now())
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $verification) {
                return false;
            }

            /*
             * Maximum 5 attempts.
             */
            if ($verification->attempts >= 5) {
                return false;
            }

            /*
             * Count this attempt.
             */
            $verification->increment('attempts');

            /*
             * Check hashed OTP.
             */
            if (! Hash::check($code, $verification->code)) {
                return false;
            }

            /*
             * Mark OTP as consumed.
             */
            $verification->update([
                'consumed_at' => now(),
            ]);

            return true;
        });
    }
}
