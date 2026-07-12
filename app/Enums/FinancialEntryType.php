<?php

namespace App\Enums;

enum FinancialEntryType: string
{
    case Debit = 'debit';     // بدهکار
    case Credit = 'credit';   // بستانکار
}
