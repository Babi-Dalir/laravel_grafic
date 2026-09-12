<?php

namespace App\Services\Message\SMS;

use App\Services\Message\MessageInterface;
use Illuminate\Support\Facades\Log;

class ServiceSMS implements MessageInterface
{
    public function __construct(
        private ?string $receiver = null,
        private ?string $content = null
    ) {
    }

    public function sendMessage(): void
    {
        if (! config('services.sms.enabled')) {

            Log::warning('SMS is disabled');

            return;
        }

        $melipayamak = new ServiceMelipayamak();

        $success = $melipayamak->sendSMS(
            $this->receiver,
            $this->content
        );

        if (! $success) {
            throw new \RuntimeException(
                'SMS provider failed to send message.'
            );
        }
    }
}
