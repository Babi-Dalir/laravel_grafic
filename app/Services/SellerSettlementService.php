<?php

namespace App\Services;

use App\Enums\SettlementStatus;
use App\Enums\TransactionType;
use App\Enums\WalletTransactionStatus;
use App\Models\Seller;
use App\Models\SellerSettlement;
use App\Models\SellerWalletTransaction;
use Illuminate\Support\Facades\DB;

class SellerSettlementService
{
    public static function run(): void
    {
        Seller::query()
            ->whereHas('transactions', function ($q) {
                $q->where('type', TransactionType::Sale->value)
                    ->where('status', WalletTransactionStatus::Pending->value)
                    ->where('release_at', '<=', now())
                    ->whereNull('settlement_id');
            })
            ->chunkById(100, function ($sellers) {

                foreach ($sellers as $seller) {

                    DB::transaction(function () use ($seller) {

                        // 🔐 قفل فروشنده برای جلوگیری از race condition
                        $seller = Seller::query()
                            ->where('id', $seller->id)
                            ->lockForUpdate()
                            ->first();

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

                        if ($totalAmount < 100000) {
                            return;
                        }

                        // 🧠 reference پایدار (مهم‌ترین اصلاح)
                        $period = now()->format('Y-m'); // ماهانه مثل مکتب‌خونه

                        $referenceId = "seller_{$seller->id}_{$period}";

                        $settlement = SellerSettlement::query()->firstOrCreate(
                            [
                                'reference_id' => $referenceId,
                            ],
                            [
                                'seller_id' => $seller->id,
                                'amount' => 0,
                                'status' => SettlementStatus::Pending->value,
                            ]
                        );

                        // 🔥 جمع کردن مبلغ (idempotent safe)
                        $settlement->increment('amount', $totalAmount);

                        // 🔥 اتصال تراکنش‌ها
                        SellerWalletTransaction::query()
                        ->whereIn('id', $transactions->pluck('id'))
                            ->update([
                                'settlement_id' => $settlement->id,
                            ]);
                    });
                }
            });
    }
}
