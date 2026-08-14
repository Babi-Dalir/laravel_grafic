<?php

namespace App\Jobs;

use App\Enums\TransactionType;
use App\Enums\WalletTransactionStatus;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchSellerSettlementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'settlements';
    public int $timeout = 300;

    public function handle(): void
    {
        // یافتن فروشندگانی که حداقل یک تراکنش آماده تسویه دارند
        Seller::query()
            ->whereHas('transactions', function ($q) {
                $q->where('type', TransactionType::Sale->value)
                    ->where('status', WalletTransactionStatus::Pending->value)
                    ->where('release_at', '<=', now())
                    ->whereNull('settlement_id');
            })
            ->select('id')
            ->chunkById(100, function ($sellers) {
                foreach ($sellers as $seller) {
                    // 🟢 ارسال جاب مجزا برای هر فروشنده به صف
                    ProcessSingleSellerSettlementJob::dispatch($seller->id);
                }
            });
    }
}
