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
     * اختصاص به صف مجزا برای تداخل نداشتن با کارهای دیگر
     */
    public string $queue = 'settlements';

    public int $tries = 3;
    public int $timeout = 120;

    public function backoff(): array
    {
        return [30, 120];
    }

    public function __construct(public int $sellerId)
    {
    }

    public function handle(): void
    {
        DB::transaction(function () {

            // 🔐 ۱. قفل فروشنده برای جلوگیری از Race Condition
            $seller = Seller::query()
                ->where('id', $this->sellerId)
                ->lockForUpdate()
                ->first();

            if (! $seller) {
                return;
            }

            // 🔐 ۲. قفل اتمیک ردیف‌های ولت کاندیدای تسویه
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

            $totalAmount = $transactions->sum('amount');

            // حد نصاب تسویه (مثلاً ۱۰۰ هزار تومان)
            if ($totalAmount < 100000) {
                return;
            }

            $period = now()->format('Y-m');
            $referenceId = "seller_{$seller->id}_{$period}";

            // 🔐 ۳. قفل لایه دیتابیس روی سند تسویه
            $settlement = SellerSettlement::query()
                ->where('reference_id', $referenceId)
                ->where('status', SettlementStatus::Pending->value)
                ->lockForUpdate()
                ->first();

            if (! $settlement) {
                $settlement = SellerSettlement::query()->create([
                    'reference_id' => $referenceId,
                    'seller_id' => $seller->id,
                    'amount' => 0,
                    'status' => SettlementStatus::Pending->value,
                ]);
            }

            // 🔥 افزایش اتمیک موجودی فاکتور تسویه
            $settlement->increment('amount', $totalAmount);

            // 🔥 اتصال شناسه فاکتور به تراکنش‌ها
            SellerWalletTransaction::query()
                ->whereIn('id', $transactions->pluck('id'))
                ->update([
                    'settlement_id' => $settlement->id,
                ]);

            Log::info("تسویه حساب ماهانه برای فروشنده {$seller->id} با موفقیت محاسبه شد.", [
                'amount' => $totalAmount,
                'settlement_id' => $settlement->id
            ]);
        });
    }

    public function failed(Throwable $exception): void
    {
        Log::error("خطای قطعی در پردازش تسویه‌حساب فروشنده {$this->sellerId}", [
            'error' => $exception->getMessage()
        ]);
    }
}
