<?php

namespace App\Models;

use App\Enums\DownloadStatus;
use App\Exceptions\DownloadLimitException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Downloads extends Model
{
    // ستون‌های مجاز برای ساخت و آپدیت گروهی (دقیقاً بر اساس مایگریشن شما)
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

    /**
     * بررسی وضعیت دانلود (برای استفاده در لایه فرانت/UI)
     */
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


    public function registerDownload($ip, $userAgent)
    {

        $affected = self::where('id', $this->id)
            ->whereRaw('download_count < max_download')
            ->update([
                'download_count' => DB::raw('download_count + 1'),
                'ip_address'     => $ip,
                'user_agent'     => $userAgent
            ]);

        // ۲. اگر دیتابیس هیچ سطری را آپدیت نکرد (Affected Rows == 0)، یعنی سقف پر بوده است
        if ($affected === 0) {
            throw new DownloadLimitException('سقف تعداد دانلود شما برای این محصول به پایان رسیده است.');
        }

        $this->refresh();
    }

    /**
     * ساخت ساختار دانلود اولیه پس از خرید موفق فاکتور
     */
    public static function createDownload(OrderDetail $order_detail, int $userId): self
    {
        return self::query()->create([
            'user_id'         => $userId,
            'product_id'      => $order_detail->product_id,
            'order_detail_id' => $order_detail->id,
            'token'           => Str::random(64), // طول توکن در مایگریشن ۱۲۰ است، پس ۶۴ کاراکتر کاملاً ایمن جا می‌شود
            'max_download'    => 5,
            'expire_at'       => now()->addYear(),
            'status'          => DownloadStatus::Active,
        ]);
    }
}
