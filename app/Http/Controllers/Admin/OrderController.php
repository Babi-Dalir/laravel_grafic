<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orders()
    {
        $title = "سفارشات";
        return view('admin.orders.order_list', compact('title'));
    }

    public function orderDetails(Order $order)
    {
        $title = "جزئیات سفارش";
        return view('admin.orders.order_detail_list', compact('title','order'));
    }
}
