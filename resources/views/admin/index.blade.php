@extends('admin.layouts.master')

@section('content')
    <div class="main-content p-4">

        {{-- 🟢 ویجت هوشمند نمایش وضعیت رشد روزانه مارکت پلتفرم --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center">
                            <i class="ti-pulse text-primary mr-3" style="font-size: 24px;"></i>
                            <span class="text-dark font-weight-bold">وضعیت ترافیک مالی امروز بازار:</span>
                        </div>
                        <div>
                            @if($insights['trend'] === 'up')
                                <span class="badge badge-success px-3 py-2 font-numeric">
                                    <i class="ti-arrow-up mr-1"></i> {{ $insights['sales_growth_today'] }}% رشد نسبت به دیروز
                                </span>
                            @else
                                <span class="badge badge-danger px-3 py-2 font-numeric">
                                    <i class="ti-arrow-down mr-1"></i> {{ abs($insights['sales_growth_today']) }}% افت نسبت به دیروز
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI CARDS - تفکیک هوشمند جریان مالی مارکت‌پلیس و درآمد پلتفرم --}}
        <div class="row g-3">

            {{-- کارت اول: سفارشات --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 border-right border-primary" style="border-right-width: 4px !important;">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <small class="text-muted font-weight-bold">
                            <i class="ti-shopping-cart mr-1 text-primary"></i> تعداد تراکنش‌های موفق امروز
                        </small>
                        <h3 class="mt-2 mb-0 font-weight-bold font-numeric text-dark">
                            {{ number_format($kpis['today_orders']) }} <small style="font-size: 14px">سفارش</small>
                        </h3>
                    </div>
                </div>
            </div>

            {{-- کارت دوم: حجم فروش روزانه فروشندگان --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 border-right border-info" style="border-right-width: 4px !important;">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <small class="text-muted font-weight-bold">
                            <i class="ti-user mr-1 text-info"></i> فروش محصولات فروشندگان (امروز)
                        </small>
                        <h3 class="mt-2 mb-0 font-weight-bold font-numeric text-info">
                            {{ number_format($kpis['today_sales']) }} <small style="font-size: 14px">تومان</small>
                        </h3>
                    </div>
                </div>
            </div>

            {{-- کارت سوم: گردش مالی ماهانه فروشندگان --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 border-right border-secondary" style="border-right-width: 4px !important;">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <small class="text-muted font-weight-bold">
                            <i class="ti-bar-chart mr-1 text-secondary"></i> کل فروش فروشندگان (ماه جاری)
                        </small>
                        <h3 class="mt-2 mb-0 font-weight-bold font-numeric text-dark">
                            {{ number_format($kpis['month_sales']) }} <small style="font-size: 14px">تومان</small>
                        </h3>
                    </div>
                </div>
            </div>

            {{-- کارت چهارم: درآمد و سود خالص خودِ سایت --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-dark text-white border-right border-success" style="border-right-width: 4px !important;">
                    <div class="card-body border-bottom border-secondary py-3">
                        <small class="text-success font-weight-bold">
                            <i class="ti-wallet mr-1"></i> درآمد خالص سایت (این ماه)
                        </small>
                        <p class="text-muted small mb-1" style="font-size: 10px;">(کمیسیون فروش + فروش مستقیم محصولات سایت)</p>
                        <h4 class="mt-1 mb-0 font-weight-bold font-numeric text-success">
                            {{ number_format($kpis['site_income_month']) }} <small style="font-size: 12px">تومان</small>
                        </h4>
                    </div>
                    <div class="card-body py-3">
                        <small class="text-warning font-weight-bold">
                            <i class="ti-server mr-1"></i> کل درآمد تاریخی پلتفرم (از ابتدا)
                        </small>
                        <h4 class="mt-1 mb-0 font-weight-bold font-numeric text-warning">
                            {{ number_format($kpis['total_site_income']) }} <small style="font-size: 12px">تومان</small>
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        {{-- SELLERS --}}
        <div class="row mt-4 g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">تعداد فروشندگان فعال مارکت</h6>
                        <h3 class="font-weight-bold font-numeric mt-2 text-info">{{ $sellers['active_sellers'] }} <small style="font-size: 14px">فروشنده</small></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">موجودی در انتظار تسویه (Pending)</h6>
                        <h3 class="font-weight-bold font-numeric mt-2 text-warning">{{ number_format($sellers['pending_balance']) }} <small style="font-size: 14px">تومان</small></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">مجموع مبالغ تسویه شده (Settled)</h6>
                        <h3 class="font-weight-bold font-numeric mt-2 text-success">{{ number_format($sellers['settled_balance']) }} <small style="font-size: 14px">تومان</small></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHART --}}
        <div class="card mt-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="font-weight-bold mb-3"><i class="ti-bar-chart text-primary mr-2"></i> نمودار فروش سالانه محصولات</h5>
                <canvas id="chart" height="100"></canvas>
            </div>
        </div>

        {{-- LATEST DATA TABLES --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-dark"><i class="ti-shopping-cart text-primary mr-2"></i> آخرین سفارش‌های ثبت‌شده</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <tbody>
                                @foreach($latest['latest_orders'] as $order)
                                    <tr>
                                        <td class="py-3 px-4 font-numeric"><strong>#{{ $order->id }}</strong></td>
                                        <td class="text-secondary font-weight-bold">{{ $order->user->name ?? 'کاربر مهمان' }}</td>
                                        <td class="text-left px-4">
                                            <span class="badge badge-light font-numeric">{{ verta($order->created_at)->format('H:i') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-dark"><i class="ti-wallet text-success mr-2"></i> آخرین تسویه‌حساب‌های مالی</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <tbody>
                                @foreach($latest['latest_settlements'] as $settlement)
                                    <tr>
                                        <td class="py-3 px-4 font-weight-bold text-secondary">
                                            {{ $settlement->seller?->first_name ? $settlement->seller->first_name . ' ' . $settlement->seller->last_name : ($settlement->seller?->user?->name ?? 'فروشنده ناشناس') }}
                                        </td>
                                        <td class="text-muted font-numeric">{{ $settlement->seller?->user?->mobile ?? '---' }}</td>
                                        <td class="text-left px-4 font-numeric text-success font-weight-bold">
                                            {{ number_format($settlement->amount) }} <small style="font-size: 11px">تومان</small>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        const ctx = document.getElementById('chart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chart['months']),
                datasets: [{
                    label: 'حجم فروش دفتری (تومان)',
                    data: @json($chart['sales']),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    </script>
@endsection
