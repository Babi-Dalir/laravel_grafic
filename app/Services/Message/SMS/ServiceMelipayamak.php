<?php

namespace App\Services\Message\SMS;

use Illuminate\Support\Facades\Log;
use Melipayamak;

class ServiceMelipayamak
{
    public function sendSMS(string $receiver, string $content): bool
    {
        try {
            $sms = Melipayamak::sms();

            $response = $sms->send(
                $receiver,
                '50004001014554',
                $content
            );

            // 🟢 بررسی پاسخ ملی‌پیامک: کد خروجی موفقیت معمولاً یک RecId بزرگ است (طول بیشتر از ۱۵ رقم یا عدد مثبت بزرگ)
            // اگر پاسخ عددی منفی یا حاوی خطا باشد، ارسال ناموفق بوده است.
            if (! $response || (is_numeric($response) && (int) $response < 15)) {
                Log::error('SMS provider returned error response', [
                    'receiver' => $receiver,
                    'response' => $response,
                ]);

                return false;
            }

            Log::info('SMS sent successfully', [
                'receiver' => $receiver,
                'response' => $response,
            ]);

            return true;

        } catch (\Throwable $e) {

            Log::error('SMS provider failed', [
                'receiver' => $receiver,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
