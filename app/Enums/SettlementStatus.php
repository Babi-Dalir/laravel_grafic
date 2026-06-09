<?php

namespace App\Enums;

enum SettlementStatus:string
{
    case Pending = 'pending';

    // پول پرداخت شده
    case Paid = 'paid';

    // درخواست رد شده
    case Rejected = 'rejected';

}
