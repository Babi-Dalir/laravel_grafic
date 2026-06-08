<?php
namespace App\Enums;

enum TransactionType: string
{
    case Sale = 'sale'; //فروش محصول

    case Withdrawal = 'withdrawal'; //برداشت

    case Refund = 'refund'; //بازگشت وجه

    case Commission = 'commission'; //کسر کمیسیون

    case Adjustment = 'adjustment'; //اصلاح توسط مدیر

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'فروش محصول',
            self::Withdrawal => 'برداشت وجه',
            self::Refund => 'بازگشت وجه',
            self::Commission => 'کسر کمیسیون',
            self::Adjustment => 'اصلاح دستی',
        };
    }
}
