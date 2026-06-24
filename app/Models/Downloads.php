<?php

namespace App\Models;

use App\Enums\DownloadStatus;
use App\Enums\OrderDetailStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Downloads extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'order_detail_id', 'token',
        'download_count', 'max_download', 'status', 'expire_at',
        'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'status' => DownloadStatus::class,
            'expire_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function canDownload(): bool
    {
        if ($this->status !== DownloadStatus::Active) {
            return false;
        }

        if ($this->expire_at && now()->greaterThan($this->expire_at)) {
            return false;
        }

        if ($this->download_count >= $this->max_download) {
            return false;
        }

        return true;
    }

    /**
     * 🟢 حل ایراد دوم و چهارم:
     * ۱. متد هیچ وابستگی به شیء Request ندارد و ورودی‌های صریح می‌گیرد.
     * ۲. به جای abort از پرتاب Exception برای مدیریت لایه بیزینس استفاده می‌کند.
     */
    public function registerDownload(string $ip, ?string $userAgent): void
    {
        DB::transaction(function () use ($ip, $userAgent) {
            $lockedDownload = self::query()->lockForUpdate()->findOrFail($this->id);

            if (!$lockedDownload->canDownload()) {
                throw new \RuntimeException('Download limit exceeded or link expired.');
            }

            $lockedDownload->increment('download_count', 1, [
                'ip_address' => $ip,
                'user_agent' => $userAgent
            ]);

            $lockedDownload->refresh();

            if ($lockedDownload->download_count >= $lockedDownload->max_download) {
                $lockedDownload->update([
                    'status' => DownloadStatus::Expired
                ]);

                $lockedDownload->orderDetail()->update([
                    'status' => OrderDetailStatus::Downloaded->value
                ]);
            }
        });
    }

    public static function createDownload(OrderDetail $order_detail, int $userId): self
    {
        return self::query()->create([
            'user_id' => $userId,
            'product_id' => $order_detail->product_id,
            'order_detail_id' => $order_detail->id,
            'token' => str()->random(100),
            'max_download' => 5,
            'expire_at' => now()->addYear(),
            'status' => DownloadStatus::Active,
        ]);
    }
}
