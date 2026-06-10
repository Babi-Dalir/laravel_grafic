<?php

namespace App\Services;

use App\Enums\SettlementStatus;
use App\Enums\WalletTransactionStatus;
use App\Models\SellerSettlement;
use Illuminate\Support\Facades\DB;

class SettlementManager
{
    public static function markAsPaid(SellerSettlement $settlement, int $adminId): void
    {
        DB::transaction(function () use ($settlement, $adminId) {

            // 🧠 قفل واقعی دیتابیس
            $settlement = SellerSettlement::query()
                ->where('id', $settlement->id)
                ->where('status', SettlementStatus::Pending->value)
                ->lockForUpdate()
                ->first();

            if (! $settlement) {
                return;
            }

            // 1. آپدیت تسویه
            $settlement->update([
                'status' => SettlementStatus::Paid->value,
                'paid_at' => now(),
                'paid_by' => $adminId,
            ]);

            // 2. آپدیت تراکنش‌ها
            $settlement->transactions()->update([
                'status' => WalletTransactionStatus::Settled->value,
                'settled_at' => now(),
            ]);
        });
    }
}
