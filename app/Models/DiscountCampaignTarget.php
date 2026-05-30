<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCampaignTarget extends Model
{
    protected $fillable = [
        'discount_campaign_id',
        'target_id',
        'target_type'
    ];

    public function campaign()
    {
        return $this->belongsTo(DiscountCampaign::class, 'discount_campaign_id');
    }

    /**
     * رابطه با محصول (اگر تارگت محصول باشد)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'target_id');
    }

    /**
     * رابطه با دسته‌بندی (اگر تارگت دسته باشد)
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'target_id');
    }
}
