<?php
namespace App\Enums;

enum WalletTransactionStatus: string
{
    case Pending = 'pending';
    case Settled = 'settled';
}
