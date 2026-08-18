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
use Illuminate\Support\Facades\Log;

class DispatchSellerSettlementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * حداکثر زمان اجرای Job
     */
    public int $timeout = 300;

    public function __construct()
    {
        $this->onQueue('settlements');
    }

    public function handle(): void
    {
        /*
         * فروشندگانی که حداقل یک تراکنش آماده تسویه دارند
         */
        Seller::query()
            ->whereHas('transactions', function ($query): void {
                $query
                    ->where(
                        'type',
                        TransactionType::Sale->value
                    )
                    ->where(
                        'status',
                        WalletTransactionStatus::Pending->value
                    )
                    ->where(
                        'release_at',
                        '<=',
                        now()
                    )
                    ->whereNull('settlement_id');
            })
            ->select('id')
            ->chunkById(100, function ($sellers): void {

                foreach ($sellers as $seller) {

                    /*
                     * Job جداگانه برای هر فروشنده
                     */
                    ProcessSingleSellerSettlementJob::dispatch(
                        $seller->id
                    )->onQueue('settlements');
                }
            });

        Log::info(
            'Dispatch فروشندگان آماده تسویه با موفقیت انجام شد.'
        );
    }
}
