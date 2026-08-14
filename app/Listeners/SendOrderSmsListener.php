<?php

namespace App\Listeners;

use App\Events\OrderPaidEvent;
use App\Services\Message\MessageService;
use App\Services\Message\SMS\ServiceSMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderSmsListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * حداکثر تعداد تلاش‌ها
     */
    public int $tries = 3;

    /**
     * حداکثر زمان اجرای Job
     */
    public int $timeout = 30;

    /**
     * فاصله بین تلاش‌ها
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(OrderPaidEvent $event): void
    {
        // اگر فقط SMS پرداخت موقتاً خاموش شده باشد
        if (! config('services.order_payment_sms.enabled')) {
            Log::info('Order payment SMS is temporarily disabled.', [
                'order_id' => $event->order->id,
                'order_code' => $event->order->order_code,
            ]);

            return;
        }

        $order = $event->order;

        $user = $order->user;

        if (! $user || ! $user->mobile) {
            return;
        }

        $userName = $user->name ?: 'کاربر عزیز';

        $content = "{$userName} گرامی، پرداخت سفارش شما به شماره {$order->order_code} با موفقیت تایید شد.";

        $smsProvider = new ServiceSMS(
            $user->mobile,
            $content
        );

        $messageService = new MessageService($smsProvider);

        $messageService->send();
    }

    public function failed(
        OrderPaidEvent $event,
        \Throwable $exception
    ): void {
        Log::critical('Order payment SMS permanently failed.', [
            'order_id' => $event->order->id,
            'order_code' => $event->order->order_code,
            'error' => $exception->getMessage(),
        ]);
    }
}
