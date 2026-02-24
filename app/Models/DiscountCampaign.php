<?php

namespace App\Models;

use App\Enums\DiscountCampaignType;
use App\Helpers\DateManager;
use Illuminate\Database\Eloquent\Model;
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
                            'target_id' => $id
                        ]);
                    }
                }
            }

            return $campaign;
        });
    }

    public static function updateCampaign($request,$id)
    {
        return DB::transaction(function () use ($request,$id) {
            // ۱. آپدیت کردن اطلاعات اصلی
            $campaign = self::findOrFail($id);
            $campaign->update([
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'percent' => $request->input('percent'),
                'priority' => self::determinePriority($request->input('type')),
                'starts_at' => $request->filled('starts_at') ? DateManager::shamsi_to_miladi_campain($request->starts_at) : $campaign->starts_at,
                'expires_at' => $request->filled('expires_at') ? DateManager::shamsi_to_miladi_campain($request->expires_at) : $campaign->expires_at,
            ]);

            // ۲. پاک کردن محصولات/دسته‌های قبلی و ثبت موارد جدید
            $campaign->targets()->delete();

            if ($request->input('type') !== DiscountCampaignType::Global->value) {
                if ($request->filled('target_ids')) {
                    foreach ($request->input('target_ids') as $id) {
                        $campaign->targets()->create([
                            'target_id' => $id
                        ]);
                    }
                }
            }

            return $campaign;
        });
    }
}
