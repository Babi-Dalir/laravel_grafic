<?php

namespace App\Services;

use App\Models\Order;
use App\Models\FinancialLedger;
use App\Enums\FinancialLedgerType;
use App\Enums\FinancialEntryType;
use Illuminate\Support\Facades\DB;

class FinancialLedgerService
{
    /**
     * ثبت زنجیره اسناد حسابداری دفتر کل برای یک سفارش پرداخت شده
     */
    public static function recordOrderMetrics(Order $order, string $paymentReference): void
    {
        // ۱. ثبت سند بدهکار بانک (پرداخت مشتری)
        if ($order->total_price > 0) {
            FinancialLedger::create([
                'order_id'   => $order->id,
                'type'       => FinancialLedgerType::CustomerPayment->value,
                'entry_type' => FinancialEntryType::Debit->value,
                'amount'     => $order->total_price,
                'description'=> "دریافت وجه فاکتور {$order->order_code} مرجع بانکی: {$paymentReference}"
            ]);
        }

        // ۲. ثبت سند هزینه کوپن تخفیف (بدهکار)
        $discountUsage = DB::table('discount_usages')
            ->where('order_id', $order->id)
            ->where('status', \App\Enums\DiscountUsageStatus::Reserved->value)
            ->first();

        if ($discountUsage && $order->discount_price > 0) {
            FinancialLedger::create([
                'order_id'   => $order->id,
                'type'       => FinancialLedgerType::CouponExpense->value,
                'entry_type' => FinancialEntryType::Debit->value,
                'amount'     => $order->discount_price,
                'description'=> "هزینه اعمال کد تخفیف پلتفرم بابت سفارش {$order->order_code}"
            ]);
        }

        // ۳. ثبت سند هزینه کارت هدیه (بدهکار)
        if ($order->gift_cart_price > 0) {
            FinancialLedger::create([
                'order_id'   => $order->id,
                'type'       => FinancialLedgerType::GiftCartExpense->value,
                'entry_type' => FinancialEntryType::Debit->value,
                'amount'     => $order->gift_cart_price,
                'description'=> "استفاده از موجودی کارت هدیه بابت سفارش {$order->order_code}"
            ]);
        }

        // ۴. ثبت اسناد ذینفعان (بستانکاران / درآمدها)
        foreach ($order->orderDetails as $detail) {
            if ($detail->seller_share > 0) {
                FinancialLedger::create([
                    'order_id'   => $order->id,
                    'type'       => FinancialLedgerType::SellerShare->value,
                    'entry_type' => FinancialEntryType::Credit->value,
                    'amount'     => $detail->seller_share,
                    'description'=> "سهم فروشنده بابت آیتم فاکتور #{$detail->id}"
                ]);
            }

            if ($detail->site_share > 0) {
                FinancialLedger::create([
                    'order_id'   => $order->id,
                    'type'       => FinancialLedgerType::SiteShare->value,
                    'entry_type' => FinancialEntryType::Credit->value,
                    'amount'     => $detail->site_share,
                    'description'=> "درآمد کارمزد سایت بابت آیتم فاکتور #{$detail->id}"
                ]);
            }

            if ($detail->platform_subsidy > 0) {
                FinancialLedger::create([
                    'order_id'   => $order->id,
                    'type'       => FinancialLedgerType::PlatformSubsidy->value,
                    'entry_type' => FinancialEntryType::Debit->value,
                    'amount'     => $detail->platform_subsidy,
                    'description'=> "سوبسید پلتفرم بابت کوپن روی فاکتور #{$detail->id}"
                ]);
            }
        }
    }
}
