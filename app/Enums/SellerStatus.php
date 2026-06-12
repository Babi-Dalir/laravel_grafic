<?php
namespace App\Enums;

enum SellerStatus:string
{

    case Pending = 'pending';
    case Active = 'active';

    case Suspended = 'suspended'; // مسدود کردن فروشنده

    case Rejected = 'rejected';
}
