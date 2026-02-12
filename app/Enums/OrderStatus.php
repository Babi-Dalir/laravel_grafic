<?php
namespace App\Enums;

enum OrderStatus: string
{
    case WaitPayment = 'wait_payment';
    case Payed = 'payed';
    case Failed = 'failed';
    case Processing = 'processing';
    case SendOrder = 'send_order';
    case ReceivedOrder = 'received_order';
    case NotReceivedOrder = 'not_received_order';
}
