<?php

namespace App\Models;

use App\Enums\DiscountCampaignStatus;
use App\Enums\DiscountCampaignType;
use App\Helpers\DateManager;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DiscountCampaign extends Model
{
    protected $fillable = [
        'name',
        'type',
        'percent',
        'priority',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => DiscountCampaignStatus::class,
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function targets()
    {
        return $this->hasMany(DiscountCampaignTarget::class, 'discount_campaign_id');
    }

    private static function determinePriority($type)
    {
        if ($type === DiscountCampaignType::Product->value) {
            return 3;
        }

        if ($type === DiscountCampaignType::Category->value) {
            return 2;
        }

        return 1;
    }

    public static function createCampaign($request)
    {
        return DB::transaction(function () use ($request) {
            // ۱. محاسبه تاریخ انقضا و ست کردن روی انتهای همان روز (23:59:59)
            $expiresAt = $request->filled('expires_at')
                ? Carbon::parse(DateManager::shamsi_to_miladi_campain($request->expires_at))->endOfDay()
                : null;

            $startsAt = $request->filled('starts_at')
                ? DateManager::shamsi_to_miladi_campain($request->starts_at)
                : now();

            return self::create([
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'percent' => $request->input('percent'),
                'priority' => self::determinePriority($request->input('type')),
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
            ]);
        });
    }

    public static function updateCampaign($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $campaign = self::findOrFail($id);

            // ۱. استخراج تاریخ انقضای جدید به میلادی در صورت تغییر
            $expiresAt = $request->filled('expires_at')
                ? Carbon::parse(DateManager::shamsi_to_miladi_campain($request->expires_at))->endOfDay()
                : $campaign->expires_at;

            // ۲. تعیین وضعیت هوشمند
            $status = $campaign->status; // حفظ Enum فعلی

            if ($request->filled('expires_at')) {
                if ($expiresAt && $expiresAt->greaterThanOrEqualTo(now())) {
                    $status = DiscountCampaignStatus::Active;
                } else {
                    $status = DiscountCampaignStatus::InActive;
                }
            } else {
                if ($expiresAt && $expiresAt->lessThan(now())) {
                    $status = DiscountCampaignStatus::InActive;
                }
            }

            // ۳. آپدیت اطلاعات
            $campaign->update([
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'percent' => $request->input('percent'),
                'status' => $status->value,
                'priority' => self::determinePriority($request->input('type')),
                'starts_at' => $request->filled('starts_at') ? DateManager::shamsi_to_miladi_campain($request->starts_at) : $campaign->starts_at,
                'expires_at' => $expiresAt,
            ]);

            // ۴. پاک‌سازی و همگام‌سازی اهداف کمپین
            $campaign->targets()->delete();

            if ($request->input('type') !== DiscountCampaignType::Global->value) {
                if ($request->filled('target_ids')) {
                    foreach ($request->input('target_ids') as $targetId) {
                        $campaign->targets()->create([
                            'target_id' => $targetId,
                            'target_type' => $request->input('type'),
                        ]);
                    }
                }
            }

            return $campaign;
        });
    }

    /**
     * 🚀 واکشی کمپین فعالِ جاری با دقت ثانیه‌ای
     */
    public static function getActiveBannerCampaign()
    {
        return self::query()
            ->where('status', DiscountCampaignStatus::Active->value)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->first();
    }
}
