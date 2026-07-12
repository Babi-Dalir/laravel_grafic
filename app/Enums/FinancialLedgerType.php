<?php

namespace App\Enums;

enum FinancialLedgerType: string
{
    case CustomerPayment = 'customer_payment'; //پرداخت مشتری
    case SellerShare = 'seller_share'; //سهم فروشنده
    case SiteShare = 'site_share'; //سهم سایت
    case CouponExpense = 'coupon_expense'; //هزینه کوپن تخفیف
    case GiftCartExpense = 'gift_cart_expense'; //هزینه کارت هدیه
    case PlatformSubsidy = 'platform_subsidy'; //سوبسید(کمک هزینه) پلتفرم
}
