<?php
namespace App\Enums;

enum OrderStatus: string
{
    case WaitPayment = 'wait_payment'; // در انتظار پرداخت
    case Payed = 'payed';             // پرداخت موفق
    case Failed = 'failed';           // پرداخت ناموفق
    case Cancelled = 'cancelled';     // لغو شده
}
