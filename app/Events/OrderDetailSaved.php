<?php

namespace App\Events;

use App\Models\OrderDetail;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderDetailSaved
{
    use Dispatchable, SerializesModels;

    public function __construct(public OrderDetail $orderDetail) {}
}
