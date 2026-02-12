<?php
namespace App\Enums;

enum ProductStatus: string
{
    case Waiting = 'waiting';
    case Active = 'active';
    case InActive = 'inactive';
    case StopProduction = 'stop_production';
    case Rejected = 'rejected';
}
