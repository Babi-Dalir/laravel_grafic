<?php

namespace App\Enums;

enum DiscountUsageStatus: string
{
    case Reserved = 'reserved';
    case Used = 'used';
    case Cancelled = 'cancelled';
}
