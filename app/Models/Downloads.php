<?php

namespace App\Models;

use App\Enums\DownloadStatus;
use Illuminate\Database\Eloquent\Model;

class Downloads extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_detail_id',
        'token',
        'download_count',
        'max_download',
        'status',
        'expire_at',
        'ip_address',
        'user_agent',
    ];
    protected $casts = [
        'expire_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
    public function canDownload()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (
            $this->expire_at &&
            now()->greaterThan($this->expire_at)
        ) {
            return false;
        }

        if (
            $this->download_count >=
            $this->max_download
        ) {
            return false;
        }

        return true;
    }

    public static function createDownload(OrderDetail $orderDetail): self
    {
        return self::create([
            'user_id' => $orderDetail->order->user_id,
            'product_id' => $orderDetail->product_id,
            'order_detail_id' => $orderDetail->id,

            'token' => str()->random(100),

            'max_download' => 5,

            'expire_at' => now()->addYear(),

            'status' => DownloadStatus::Active->value,
        ]);
    }
}
