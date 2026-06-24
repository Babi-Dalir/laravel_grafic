<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaidEvent
{
    use Dispatchable, SerializesModels;

    public Order $order;

    /**
     * تزریق مدل سفارش به رویداد
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
