<?php

namespace App\Enums;

enum SettlementStatus:string
{
    // فروشنده درخواست داده
    case Pending = 'pending';

    // مدیر تایید کرده و منتظر پرداخت است
    case Approved = 'approved';

    // پول پرداخت شده
    case Paid = 'paid';

    // درخواست رد شده
    case Rejected = 'rejected';

    // لغو توسط فروشنده
    case Cancelled = 'cancelled';
}
