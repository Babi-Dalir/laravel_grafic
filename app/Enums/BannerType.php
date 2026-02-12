<?php
namespace App\Enums;

enum BannerType: string
{
    case TopBanner = 'top_banner';
    case SideBanner = 'side_banner';
    case MediumBanner = 'medium_banner';
    case SmallBanner = 'small_banner';
    case LargeBanner = 'large_banner';
}
