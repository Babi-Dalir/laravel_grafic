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

    /**
     * 🟢 تضمین ذخیره‌سازی انقضا در آخرین ثانیه روز مشخص شده
     */
    protected function expiresAt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                return Carbon::parse($value);
            },
            set: function ($value) {
                if (!$value) return null;
                // تبدیل تاریخ ورودی به شیء کربن و انتقال به آخرین ثانیه آن روز (23:59:59) جهت حل مشکل تایمر
                return Carbon::parse($value)->endOfDay();
            }
        );
    }


    public function targets()
    {
        return $this->hasMany(DiscountCampaignTarget::class, 'discount_campaign_id');
    }
    // این متد را به انتهای مدل اضافه کن
    private static function determinePriority($type)
    {
        // اگر نوع کمپین "محصول" بود، اولویت را 3 بگذار (بیشترین اهمیت)
        if ($type === DiscountCampaignType::Product->value) {
            return 3;
        }

        // اگر نوع کمپین "دسته‌بندی" بود، اولویت را 2 بگذار
        if ($type === DiscountCampaignType::Category->value) {
            return 2;
        }

        // در غیر این صورت (مثلاً Global)، اولویت را 1 بگذار (کمترین اهمیت)
        return 1;
    }

    public static function createCampaign($request)
    {
        return DB::transaction(function () use ($request) {
            // ۱. ذخیره اطلاعات اصلی کمپین
            $campaign = self::create([
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'percent' => $request->input('percent'),
                'priority' => self::determinePriority($request->input('type')),
                'starts_at' => $request->filled('starts_at') ? DateManager::shamsi_to_miladi_campain($request->starts_at) : now(),
                'expires_at' => $request->filled('expires_at') ? DateManager::shamsi_to_miladi_campain($request->expires_at) : null,
            ]);

            // ۲. اگر نوع کمپین 'global' نبود، محصولات یا دسته‌های انتخاب شده را وصل کن
            if ($request->input('type') !== DiscountCampaignType::Global->value) {
                if ($request->filled('target_ids')) {
                    foreach ($request->input('target_ids') as $id) {
                        $campaign->targets()->create([
                            'target_id' => $id,
                            'target_type' => $request->input('type'),
                        ]);
                    }
                }
            }

            return $campaign;
        });
    }

    public static function updateCampaign($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $campaign = self::findOrFail($id);

            // ۱. استخراج تاریخ انقضای جدید به میلادی
            $expiresAt = $request->filled('expires_at')
                ? DateManager::shamsi_to_miladi_campain($request->expires_at)
                : $campaign->expires_at;

            // ۲. تعیین وضعیت هوشمند و منعطف
            $status = $campaign->status->value; // وضعیت فعلی کمپین را به عنوان پیش‌فرض نگه می‌داریم

            if ($request->filled('expires_at')) {
                // الف) اگر مدیر تاریخ انقضای جدیدی ثبت کرده است:
                $carbonExpires = Carbon::parse($expiresAt);

                if ($carbonExpires->greaterThanOrEqualTo(now())) {
                    $status = DiscountCampaignStatus::Active->value;
                } else {
                    $status = DiscountCampaignStatus::InActive->value;
                }
            } else {
                // ب) اگر تاریخ انقضا ویرایش نشده و خالی مانده است:
                // اگر کمپین فعال بوده، بگذار فعال بماند.
                // فقط اگر تاریخ انقضای قبلی رد شده باشد، آن را غیرفعال کن.
                if ($expiresAt) {
                    $carbonExpires = Carbon::parse($expiresAt);
                    if ($carbonExpires->lessThan(now())) {
                        $status = DiscountCampaignStatus::InActive->value;
                    }
                }
            }

            // ۳. آپدیت اطلاعات اصلی
            $campaign->update([
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'percent' => $request->input('percent'),
                'status' => $status, // اعمال وضعیت هوشمند اصلاح‌شده
                'priority' => self::determinePriority($request->input('type')),
                'starts_at' => $request->filled('starts_at') ? DateManager::shamsi_to_miladi_campain($request->starts_at) : $campaign->starts_at,
                'expires_at' => $expiresAt,
            ]);

            // ۴. پاک کردن محصولات/دسته‌های قبلی و ثبت موارد جدید
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

            // گارد زمان شروع: یا شروع ندارد یا زمان شروعش فرا رسیده است
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })

            // 🟢 اصلاح شد: گارد زمان پایان با مقایسه دقیق ساعت و ثانیه جاری سرور
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now()); // استفاده از now() به جای today()
            })
            ->latest()
            ->first();
    }
}
