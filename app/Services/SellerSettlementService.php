<?php

namespace App\Services;

use App\Enums\SettlementStatus;
use App\Enums\TransactionType;
use App\Models\Seller;
use App\Models\SellerSettlement;
use App\Models\SellerWalletTransaction;
use Illuminate\Support\Facades\DB;

class SellerSettlementService
{
    public static function run(): void
    {
        Seller::query()
            ->whereHas('transactions', function ($query) {
                $query->where('type', TransactionType::Sale->value)
                    ->where('status', 'pending')
                    ->where('release_at', '<=', now());
            })
            ->chunkById(100, function ($sellers) {

                foreach ($sellers as $seller) {

                    DB::transaction(function () use ($seller) {

                        $transactions = SellerWalletTransaction::query()
                            ->where('seller_id', $seller->id)
                            ->where('type', TransactionType::Sale->value)
                            ->where('status', 'pending')
                            ->where('release_at', '<=', now())
                            ->lockForUpdate()
                            ->get();

                        if ($transactions->isEmpty()) {
                            return;
                        }

                        $totalAmount = $transactions->sum('amount');

                        if ($totalAmount < 100000) {
                            return;
                        }

                        $referenceId =
                            'seller_' .
                            $seller->id .
                            '_' .
                            $transactions->min('id') .
                            '_' .
                            $transactions->max('id');

                        // جلوگیری از ساخت مجدد
                        if (
                            SellerSettlement::where(
                                'reference_id',
                                $referenceId
                            )->exists()
                        ) {
                            return;
                        }

                        SellerSettlement::create([
                            'seller_id' => $seller->id,
                            'amount' => $totalAmount,
                            'status' => SettlementStatus::Pending->value,
                            'reference_id' => $referenceId,
                        ]);
                    });
                }
            });
    }
}
