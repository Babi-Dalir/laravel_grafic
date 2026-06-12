@extends('admin.layouts.master')

@section('content')

    <div class="main-content p-4">

        {{-- KPI CARDS --}}
        <div class="row g-3">

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <small>سفارش امروز</small>
                        <h3>{{ $kpis['today_orders'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <small>فروش امروز</small>
                        <h3>{{ number_format($kpis['today_sales']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <small>فروش ماه</small>
                        <h3>{{ number_format($kpis['month_sales']) }}</h3>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <small>کل درآمد سایت</small>
                        <h3>{{ number_format($kpis['total_site_income']) }}</h3>
                    </div>
                    <div class="card-body">
                        <small>درآمد ماهانه سایت</small>
                        <h3>{{ number_format($kpis['site_income_month']) }}</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- SELLERS --}}
        <div class="row mt-4 g-3">

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6>فروشندگان فعال</h6>
                        <h3>{{ $sellers['active_sellers'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6>در انتظار تسویه</h6>
                        <h3>{{ number_format($sellers['pending_balance']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6>تسویه شده</h6>
                        <h3>{{ number_format($sellers['settled_balance']) }}</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- CHART --}}
        <div class="card mt-4 shadow-sm border-0">
            <div class="card-body">
                <h5>نمودار فروش سالانه</h5>
                <canvas id="chart"></canvas>
            </div>
        </div>

        {{-- LATEST --}}
        <div class="row mt-4">

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5>آخرین سفارش‌ها</h5>

                        @foreach($latest['latest_orders'] as $order)
                            <div class="border-bottom py-2">
                                #{{ $order->id }} - {{ $order->user->name ?? '---' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5>آخرین تسویه‌ها</h5>

                        @foreach($latest['latest_settlements'] as $settlement)
                            <div class="border-bottom py-2">
                                {{ $settlement->seller->user->name ?? '---' }}
                                {{ $settlement->seller->user->mobile ?? '---' }}
                            </div>
                        @endforeach
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
                    label: 'Sales',
                    data: @json($chart['sales']),
                    borderColor: '#3b82f6',
                    tension: 0.4
                }]
            },
            options: {
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        console.log("month clicked:", index);
                    }
                }
            }
        });
    </script>

@endsection
