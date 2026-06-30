<?php

namespace App\Enums;

enum DashboardCacheKey: string
{
    case Kpis = 'admin.panel.kpis';
    case Sellers = 'admin.panel.sellers';
    case Latest = 'admin.panel.latest';
    case MonthlySales = 'admin.panel.monthly_sales';
    case Insights = 'admin.panel.insights'; // 🟢 یکپارچه شد
}
