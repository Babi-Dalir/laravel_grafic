<?php

namespace App\Jobs;

use App\Enums\SettlementStatus;
use App\Enums\TransactionType;
use App\Enums\WalletTransactionStatus;
use App\Models\Seller;
use App\Models\SellerSettlement;
use App\Models\SellerWalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessSingleSellerSettlementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * تعداد تلاش مجدد در صورت خطا
     */
    public int $tries = 3;

    /**
     * حداکثر زمان اجرای Job بر حسب ثانیه
     */
    public int $timeout = 120;

    /**
     * زمان‌های انتظار بین تلاش‌های مجدد
     */
    public function backoff(): array
    {
        return [30, 120];
    }

    public function __construct(
        public int $sellerId
    ) {
        $this->onQueue('settlements');
    }

    public function handle(): void
    {
        DB::transaction(function (): void {

            /*
             * 1. قفل فروشنده
             *
             * این قفل باعث می‌شود اگر همزمان چند Job
             * برای یک فروشنده اجرا شدند، عملیات تسویه
             * به صورت همزمان روی همان فروشنده انجام نشود.
             */
            $seller = Seller::query()
                ->whereKey($this->sellerId)
                ->lockForUpdate()
                ->first();

            if (! $seller) {
                Log::warning(
                    "فروشنده {$this->sellerId} برای تسویه پیدا نشد."
                );

                return;
            }

            /*
             * 2. پیدا کردن تراکنش‌های آماده تسویه
             */
            $transactions = SellerWalletTransaction::query()
                ->where('seller_id', $seller->id)
                ->where('type', TransactionType::Sale->value)
                ->where('status', WalletTransactionStatus::Pending->value)
                ->where('release_at', '<=', now())
                ->whereNull('settlement_id')
                ->lockForUpdate()
                ->get();

            if ($transactions->isEmpty()) {
                return;
            }

            /*
             * 3. محاسبه مجموع تراکنش‌ها
             */
            $totalAmount = $transactions->sum('amount');

            /*
             * حداقل مبلغ مورد نیاز برای ایجاد تسویه
             */
            if ($totalAmount < 100000) {
                return;
            }

            /*
             * 4. شناسه مرجع تسویه ماه جاری
             */
            $period = now()->format('Y-m');

            $referenceId = "seller_{$seller->id}_{$period}";

            /*
             * 5. پیدا کردن تسویه Pending موجود
             */
            $settlement = SellerSettlement::query()
                ->where('reference_id', $referenceId)
                ->where('status', SettlementStatus::Pending->value)
                ->lockForUpdate()
                ->first();

            /*
             * اگر تسویه‌ای وجود نداشت، ایجاد می‌کنیم.
             */
            if (! $settlement) {
                $settlement = SellerSettlement::query()->create([
                    'reference_id' => $referenceId,
                    'seller_id' => $seller->id,
                    'amount' => 0,
                    'status' => SettlementStatus::Pending->value,
                ]);
            }

            /*
             * 6. افزایش مبلغ تسویه
             */
            $settlement->increment('amount', $totalAmount);

            /*
             * 7. اتصال تراکنش‌ها به سند تسویه
             */
            SellerWalletTransaction::query()
                ->whereIn('id', $transactions->pluck('id'))
                ->update([
                    'settlement_id' => $settlement->id,
                ]);

            /*
             * 8. ثبت Log
             */
            Log::info(
                "تسویه حساب فروشنده {$seller->id} با موفقیت محاسبه شد.",
                [
                    'seller_id' => $seller->id,
                    'amount' => $totalAmount,
                    'settlement_id' => $settlement->id,
                    'transactions_count' => $transactions->count(),
                    'reference_id' => $referenceId,
                ]
            );
        });
    }

    /**
     * زمانی که Job بعد از تمام تلاش‌ها شکست بخورد.
     */
    public function failed(Throwable $exception): void
    {
        Log::error(
            "خطای قطعی در پردازش تسویه‌حساب فروشنده {$this->sellerId}",
            [
                'seller_id' => $this->sellerId,
                'error' => $exception->getMessage(),
                'exception' => $exception::class,
            ]
        );
    }
}
