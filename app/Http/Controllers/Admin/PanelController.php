<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PanelDashboardService;

class PanelController extends Controller
{
    public function __construct(
        private PanelDashboardService $service
    ) {}

    /**
     * نمایش داشبورد مدیریت با بالاترین پرفورمنس و کش اتمیک
     */
    public function index()
    {
        // دریافت مستقیم داده‌های کش‌شده و بهینه از سرویس بدون کوئری‌های موازی و تکراری
        $data = $this->service->buildDashboard();

        return view('admin.index', $data);
    }
}
