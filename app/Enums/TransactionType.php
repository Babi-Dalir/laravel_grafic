<?php
namespace App\Enums;

enum TransactionType: string
{
    case Sale = 'sale'; //فروش محصول

    case Withdrawal = 'withdrawal'; //برداشت

    case Refund = 'refund'; //بازگشت وجه

    case Commission = 'commission'; //کسر کمیسیون

    case Adjustment = 'adjustment'; //اصلاح توسط مدیر
}
