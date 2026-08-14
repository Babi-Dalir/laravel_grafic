<?php

namespace App\Services\Message;

class MessageService
{
    public function __construct(
        private MessageInterface $message
    ) {
    }

    public function send(): void
    {
        $this->message->sendMessage();
    }
}
