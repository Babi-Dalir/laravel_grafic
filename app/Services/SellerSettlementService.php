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
                    ->whereNull('settlement_id'); // 🟢 گارد اصلی: فقط تراکنش‌های متصل‌نشده
            })
            ->chunkById(100, function ($sellers) {

                foreach ($sellers as $seller) {

                    DB::transaction(function () use ($seller) {

                        // 🔐 قفل فروشنده برای جلوگیری از race condition
                        $seller = Seller::query()
                            ->where('id', $seller->id)
                            ->lockForUpdate()
                            ->first();

                        // 🔐 قفل اتمیک ردیف‌های ولت کاندیدای تسویه
                        $transactions = SellerWalletTransaction::query()
                            ->where('seller_id', $seller->id)
                            ->where('type', TransactionType::Sale->value)
                            ->where('status', WalletTransactionStatus::Pending->value)
                            ->where('release_at', '<=', now())
                            ->whereNull('settlement_id') // 🟢 فقط آن‌هایی که فاکتور نشده‌اند
                            ->lockForUpdate()
                            ->get();

                        if ($transactions->isEmpty()) {
                            return;
                        }

                        $totalAmount = $transactions->sum('amount');

                        // حد نصاب تسویه پلتفرم شما
                        if ($totalAmount < 100000) {
                            return;
                        }

                        $period = now()->format('Y-m');
                        $referenceId = "seller_{$seller->id}_{$period}";

                        // 🧠 پچ طلایی: استفاده از قفل لایه دیتابیس برای جلوگیری از اور-اینکرمنت (بزرگترین باگ موازی)
                        $settlement = SellerSettlement::query()
                            ->where('reference_id', $referenceId)
                            ->where('status', SettlementStatus::Pending->value)
                            ->lockForUpdate()
                            ->first();

                        if (! $settlement) {
                            // اگر برای این ماه هنوز فاکتور معلقی وجود ندارد، ساخته می‌شود
                            $settlement = SellerSettlement::query()->create([
                                'reference_id' => $referenceId,
                                'seller_id' => $seller->id,
                                'amount' => 0,
                                'status' => SettlementStatus::Pending->value,
                            ]);
                        }

                        // 🔥 افزایش اتمیک موجودی فاکتور تسویه ادمین
                        $settlement->increment('amount', $totalAmount);

                        // 🔥 قفل کردن تراکنش‌ها درون این فاکتور (اتصال شناسه فاکتور)
                        SellerWalletTransaction::query()
                            ->whereIn('id', $transactions->pluck('id'))
                            ->update([
                                'settlement_id' => $settlement->id,
                                // وضعیت تا زمان پرداخت ادمین همان Pending می‌ماند اما چون settlement_id دارد امن است.
                            ]);
                    });
                }
            });
    }
}
