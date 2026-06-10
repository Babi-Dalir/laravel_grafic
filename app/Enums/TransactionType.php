<?php
namespace App\Enums;

enum TransactionType: string
{
    case Sale = 'sale'; //فروش محصول

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'فروش محصول',
        };
    }
}
