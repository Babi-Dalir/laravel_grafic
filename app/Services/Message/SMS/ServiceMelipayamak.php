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
                '50004001516624',
                $content
            );

            $data = json_decode($response, true);

            if (!is_array($data)) {
                Log::error('Invalid SMS provider response', [
                    'receiver' => $receiver,
                    'response' => $response,
                ]);

                return false;
            }

            if (($data['RetStatus'] ?? null) != 1) {
                Log::error('SMS provider rejected message', [
                    'receiver' => $receiver,
                    'response' => $data,
                ]);

                return false;
            }

            Log::info('SMS accepted by provider', [
                'receiver' => $receiver,
                'rec_id' => $data['Value'] ?? null,
                'status' => $data['StrRetStatus'] ?? null,
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
