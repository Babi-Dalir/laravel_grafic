<?php
namespace App\Enums;

enum OrderDetailStatus:string
{
    case Waiting = 'waiting';         // در انتظار پرداخت
    case Paid = 'paid';               // پرداخت شده
    case Downloaded = 'downloaded';   // دانلود شده
    case Refunded = 'refunded';       // مرجوع شده
}
