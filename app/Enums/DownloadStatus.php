<?php
namespace App\Enums;

enum DownloadStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Blocked = 'blocked';
}
