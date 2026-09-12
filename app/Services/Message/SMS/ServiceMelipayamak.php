<?php

namespace App\Services\Message\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServiceMelipayamak
{
    public function sendOTP(string $receiver): ?string
    {
        if (! config('services.sms.enabled')) {
            Log::warning('SMS service is disabled');

            return null;
        }

        $url = config('services.sms.melipayamak.otp_url');

        if (! $url) {
            Log::error('Melipayamak OTP URL is not configured');

            return null;
        }

        try {
            $response = Http::asJson()
                ->acceptJson()
                ->timeout(10)
                ->connectTimeout(5)
                ->post($url, [
                    'to' => $receiver,
                ]);

            $data = $response->json();

            if (! $response->successful()) {
                Log::error('Melipayamak OTP request failed', [
                    'http_status' => $response->status(),
                    'status' => is_array($data)
                        ? ($data['status'] ?? null)
                        : null,
                ]);

                return null;
            }

            if (
                ! is_array($data) ||
                ! isset($data['code']) ||
                ! is_string($data['code']) ||
                $data['code'] === ''
            ) {
                Log::error('Invalid Melipayamak OTP response', [
                    'http_status' => $response->status(),
                ]);

                return null;
            }

            return $data['code'];

        } catch (\Throwable $e) {
            Log::error('Melipayamak OTP exception', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
