<?php

namespace App\Listeners;

use App\Events\OrderPaidEvent;
use App\Services\Message\MessageService;
use App\Services\Message\SMS\ServiceSMS;
use Illuminate\Contracts\Queue\ShouldQueue; // 🟢 اضافه شدن اینترفیس صف
use Illuminate\Queue\InteractsWithQueue;     // 🟢 قابلیت مدیریت اجزای صف
use Illuminate\Support\Facades\Log;

class SendOrderSmsListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * تعداد دفعات تلاش مجدد در صورت شکست اتصال به پنل پیامک
     */
    public $tries = 3;

    /**
     * مدت زمان تاخیر (به ثانیه) قبل از تلاش مجدد
     */
    public $backoff = 60;

    /**
     * ارسال پیامک در بک‌گراند (کاملاً غیرهمزمان و بدون معطلی کاربر)
     */
    public function handle(OrderPaidEvent $event): void
    {
        $order = $event->order;
        $user = $order->user;

        if (!$user || !$user->mobile) {
            return;
        }

        try {
            $userName = $user->name ?? 'کاربر عزیز';
            $content = "{$userName} گرامی، پرداخت سفارش شما به شماره {$order->order_code} با موفقیت تایید شد.";

            $smsProvider = new ServiceSMS($user->mobile, $content);
            $messageService = new MessageService($smsProvider);
            $messageService->send();

        } catch (\Throwable $e) {
            Log::error("خطای پترن پیامک در صف برای سفارش {$order->order_code}: " . $e->getMessage());

            // در صورت بروز خطا، جاب را برای تلاش مجدد به صف بازمی‌گردانیم
            $this->release($this->backoff);
        }
    }
}
