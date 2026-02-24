<?php
namespace App\Enums;

enum DiscountCampaignType: string
{
    case Product = 'product';
    case Category = 'category';
    case Global = 'global';
}
