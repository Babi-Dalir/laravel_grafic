@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        <div class="row">
            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="alert alert-info">
                    <div>{{session('message')}}</div>
                </div>
            @endif

        </div>
        <div class="container-fluid">

            {{-- اطلاعات فروشنده --}}
            <div class="card mb-4">
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-2 text-center">

                            <img
                                src="{{ url('images/users/small/'.$seller->user->image) }}"
                                class="rounded-circle"
                                width="100">

                        </div>

                        <div class="col-md-10">

                            <h4>
                                {{ $seller->first_name }}
                                {{ $seller->last_name }}
                            </h4>

                            <p class="mb-1">
                                برند:
                                <strong>{{ $seller->brand_name }}</strong>
                            </p>

                            <p class="mb-1">
                                موبایل:
                                {{ $seller->user?->mobile }}
                            </p>

                            <p class="mb-0">
                                کد ملی:
                                {{ $seller->national_code }}
                            </p>

                        </div>

                    </div>

                </div>
            </div>

            {{-- آمار مالی --}}
            <div class="row mb-4">

                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">

                            <h6>در انتظار تسویه</h6>

                            <h4>
                                {{ number_format($pendingBalance) }}
                            </h4>

                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">

                            <h6>تسویه شده</h6>

                            <h4>
                                {{ number_format($settledBalance) }}
                            </h4>

                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">

                            <h6>تعداد فروش</h6>

                            <h4>
                                {{ $totalOrders }}
                            </h4>

                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">

                            <h6>تعداد محصولات</h6>

                            <h4>
                                {{ $totalProducts }}
                            </h4>

                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">

                            <h6>درآمد فروشنده</h6>

                            <h4>
                                {{ number_format($totalSellerIncome) }}
                            </h4>

                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">

                            <h6>مجموع فروش</h6>

                            <h4>
                                {{ number_format($totalSalesAmount) }}
                            </h4>

                        </div>
                    </div>
                </div>

            </div>

            {{-- فروش ها --}}
            <div class="card mb-4">

                <div class="card-header">
                    <h5 class="mb-0">
                        فروش‌های فروشنده
                    </h5>
                </div>

                <div class="card-body">

                    <table class="table table-striped">

                        <thead>
                        <tr>
                            <th>محصول</th>
                            <th>کد سفارش</th>
                            <th>قیمت</th>
                            <th>سهم سایت</th>
                            <th>سهم فروشنده</th>
                            <th>تاریخ</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($sales as $sale)

                            <tr>

                                <td>
                                    {{ $sale->product?->name }}
                                </td>

                                <td>
                                    {{ $sale->order?->order_code }}
                                </td>

                                <td>
                                    {{ number_format($sale->price) }}
                                </td>

                                <td class="text-danger">
                                    {{ number_format($sale->site_share) }}
                                </td>

                                <td class="text-success">
                                    {{ number_format($sale->seller_share) }}
                                </td>

                                <td>
                                    {{ verta($sale->created_at)->format('Y/m/d') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center">
                                    فروشی ثبت نشده
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                    {{ $sales->links() }}

                </div>

            </div>

            {{-- تسویه ها --}}
            <div class="card">

                <div class="card-header">
                    <h5 class="mb-0">
                        تسویه حساب‌ها
                    </h5>
                </div>

                <div class="card-body">

                    <table class="table table-striped">

                        <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                            <th>پرداخت کننده</th>
                            <th>تاریخ پرداخت</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($settlements as $settlement)

                            <tr>

                                <td>
                                    {{ $settlement->reference_id }}
                                </td>

                                <td>
                                    {{ number_format($settlement->amount) }}
                                </td>

                                <td>

                                    @if($settlement->status === 'paid')

                                        <span class="badge bg-success">
                                    پرداخت شده
                                </span>

                                    @else

                                        <span class="badge bg-warning">
                                    در انتظار
                                </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $settlement->user?->name ?? '---' }}
                                </td>

                                <td>

                                    @if($settlement->paid_at)

                                        {{ verta($settlement->paid_at)->format('Y/m/d') }}

                                    @else

                                        ---

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center">
                                    تسویه‌ای ثبت نشده
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                    {{ $settlements->links() }}

                </div>

            </div>

        </div>
    </main>
@endsection
