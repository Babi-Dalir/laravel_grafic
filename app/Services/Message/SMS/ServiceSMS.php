<?php

namespace App\Services\Message\SMS;

use App\Services\Message\MessageInterface;

class ServiceSMS implements MessageInterface
{
    private $reciever;
    private $content;

    // اضافه کردن اکستنشن سازنده برای راحتی کار در Listener
    public function __construct($reciever = null, $content = null)
    {
        $this->reciever = $reciever;
        $this->content = $content;
    }

    public function getContent() { return $this->content; }
    public function setContent($content): void { $this->content = $content; }

    public function getReciever() { return $this->reciever; }
    public function setReciever($reciever): void { $this->reciever = $reciever; }

    public function sendMessage()
    {
        $melipayamak = new ServiceMelipayamak();
        $melipayamak->sendSMS($this->reciever, $this->content);
    }
}
