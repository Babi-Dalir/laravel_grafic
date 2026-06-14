<?php

namespace App\Services\Message\SMS;
use Illuminate\Support\Facades\Log;
use Melipayamak;
class ServiceMelipayamak
{
//    public function sendSMS($reciever,$content)
//    {
//        try{
//            $sms = Melipayamak::sms();
//            $to = $reciever;
//            $from = '50004001014554';
//            $text = $content;
//            $response = $sms->send($to,$from,$text);
//            $json = json_decode($response);
//            echo $json->Value; //RecId or Error Number
//        }catch(\Exception $e){
//            echo $e->getMessage();
//        }
//    }

    public function sendSMS($receiver, $content): bool
    {
        try {

            $sms = Melipayamak::sms();

            $response = $sms->send(
                $receiver,
                '50004001014554',
                $content
            );

            Log::info('SMS Sent Successfully', [
                'receiver' => $receiver,
                'response' => $response
            ]);

            return true;

        } catch (\Throwable $e) {

            Log::error('SMS Failed', [
                'receiver' => $receiver,
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }
}
