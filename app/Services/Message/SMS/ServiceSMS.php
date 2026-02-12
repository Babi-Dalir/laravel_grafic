<?php

namespace App\Services\Message\SMS;

use App\Services\Message\MessageInterface;

class ServiceSMS implements MessageInterface
{
    private $reciever;
    private $content;

    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content): void
    {
        $this->content = $content;
    }
    public function getReciever()
    {
        return $this->reciever;
    }
    public function setReciever($reciever): void
    {
        $this->reciever = $reciever;
    }
    public function sendMessage()
    {
        $melipayamak = new ServiceMelipayamak();
        $melipayamak->sendSMS($this->reciever,$this->content);
    }
}
