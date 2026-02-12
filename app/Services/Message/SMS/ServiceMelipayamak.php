<?php

namespace App\Services\Message\SMS;
use Melipayamak;
class ServiceMelipayamak
{
    public function sendSMS($reciever,$content)
    {
        try{
            $sms = Melipayamak::sms();
            $to = $reciever;
            $from = '50004001014554';
            $text = $content;
            $response = $sms->send($to,$from,$text);
            $json = json_decode($response);
            echo $json->Value; //RecId or Error Number
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }
}
