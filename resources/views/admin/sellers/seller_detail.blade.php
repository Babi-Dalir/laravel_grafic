@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        <div class="row">
            @if(\Illuminate\Support\Facades\Session::has('message'))
                <div class="col-12">
                    <div class="alert alert-info alert-dismissible fade show text-right" role="alert">
                        <i class="ti-info-alt mr-2"></i> {{ session('message') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="container-fluid">
            {{-- کارت مدرن اطلاعات جامع فروشنده --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <img src="{{ $seller->user?->image ? asset('images/users/small/'.$seller->user->image) : asset('images/users/default-avatar.png') }}"
                                 class="rounded-circle img-thumbnail shadow-sm"
                                 width="110" height="110" style="object-fit: cover;"
                                 alt="پروفایل فروشنده">
                        </div>
                        <div class="col-md-10 text-left">
                            <h4 class="font-weight-bold text-dark mb-2">
                                {{ $seller->first_name ? $seller->first_name . ' ' . $seller->last_name : $seller->user?->name }}
                            </h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">نام برند تجاری: <strong class="text-info">{{ $seller->brand_name ?? 'ثبت نشده' }}</strong></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">شماره موبایل ارتباطی: <strong class="text-dark font-numeric">{{ $seller->user?->mobile ?? '---' }}</strong></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">کد ملی تایید شده: <strong class="text-dark font-numeric">{{ $seller->national_code ?? '---' }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 💳 کارت اختصاصی اطلاعات بانکی جهت واریز و تسویه‌حساب --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="ti-credit-card text-primary mr-2"></i> مشخصات حساب بانکی (جهت واریز و تسویه)
                    </h5>
                    @if($seller->bank_verified)
                        <span class="badge badge-success p-2">
                            <i class="ti-check-box mr-1"></i> حساب بانکی تایید شده
                        </span>
                    @else
                        <span class="badge badge-warning p-2">
                            <i class="ti-time mr-1"></i> در انتظار بررسی / تایید نشده
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        {{-- نام صاحب حساب --}}
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block mb-1">نام و نام خانوادگی صاحب حساب:</small>
                            <strong class="text-dark font-weight-bold">
                                {{ $seller->first_name && $seller->last_name ? $seller->first_name . ' ' . $seller->last_name : ($seller->user?->name ?? 'ثبت نشده') }}
                            </strong>
                        </div>

                        {{-- کد ملی --}}
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block mb-1">کد ملی صاحب حساب:</small>
                            <strong class="text-dark font-numeric">
                                {{ $seller->national_code ?? 'ثبت نشده' }}
                            </strong>
                        </div>

                        {{-- شماره کارت --}}
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block mb-1">شماره کارت ۱۶ رقمی:</small>
                            <div class="d-flex align-items-center">
                                <strong class="text-dark font-numeric dir-ltr mr-2">
                                    {{ $seller->card_number ? implode('-', str_split($seller->card_number, 4)) : 'ثبت نشده' }}
                                </strong>
                                @if($seller->card_number)
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" onclick="copyToClipboard('{{ $seller->card_number }}', 'شماره کارت')" title="کپی شماره کارت">
                                        <i class="ti-files"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- شماره حساب --}}
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block mb-1">شماره حساب:</small>
                            <strong class="text-dark font-numeric dir-ltr">
                                {{ $seller->account_number ?? 'ثبت نشده' }}
                            </strong>
                        </div>

                        {{-- شماره شبا (IR) --}}
                        <div class="col-md-12 mt-2">
                            <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="ti-wallet text-info font-size-20 mr-2"></i>
                                    <span class="text-muted mr-2">شماره شبا (IBAN):</span>
                                    <strong class="text-primary font-numeric dir-ltr font-size-18">
                                        {{ $seller->iban ? (str_starts_with($seller->iban, 'IR') ? $seller->iban : 'IR' . $seller->iban) : 'ثبت نشده' }}
                                    </strong>
                                </div>
                                @if($seller->iban)
                                    <button type="button" class="btn btn-sm btn-info" onclick="copyToClipboard('{{ $seller->iban }}', 'شماره شبا')">
                                        <i class="ti-files mr-1"></i> کپی شماره شبا
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🟢 گریدبندی آمار مالی با واحد پولی تومان --}}
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white h-100 shadow-xs border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">کیف پول (در انتظار تسویه)</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ number_format($pendingBalance) }} <small>تومان</small></h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-success text-white h-100 shadow-xs border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">مجموع تسویه شده</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ number_format($settledBalance) }} <small>تومان</small></h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-dark text-white h-100 shadow-xs border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">خالص درآمد فروشنده</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ number_format($totalSellerIncome) }} <small>تومان</small></h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-secondary text-white h-100 shadow-xs border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">ارزش دفتری کل محصولات</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ number_format($totalProductValue) }} <small>تومان</small></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-info text-white h-100 shadow-xs border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">کارمزد ناخالص سایت (Site Share)</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ number_format($grossSiteIncome) }} <small>تومان</small></h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-danger text-white h-100 shadow-xs border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">سوبسید اعطایی پلتفرم (کمپین‌ها)</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ number_format($platformSubsidy) }} <small>تومان</small></h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card {{ $netPlatformProfit >= 0 ? 'bg-info' : 'bg-danger' }} text-white h-100 shadow-xs border-0" style="filter: brightness(0.85)">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">سود خالص پلتفرم (Platform Profit)</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ number_format($netPlatformProfit) }} <small>تومان</small></h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white h-100 shadow-xs border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <small class="text-uppercase font-weight-bold opacity-75">تعداد سفارشات / کل محصولات</small>
                            <h4 class="mt-2 mb-0 font-weight-bold font-numeric">{{ $totalOrders }} <small>بار</small> / {{ $totalProducts }} <small>قلم</small></h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- بخش لیست فروش‌ها --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="ti-shopping-cart text-primary mr-2"></i> لیست فروش‌های رسمی فروشنده</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-right">عنوان محصول ارسالی</th>
                                <th class="text-center">کد سفارش</th>
                                <th class="text-center">قیمت محصول (تومان)</th>
                                <th class="text-center">کارمزد تئوریک سایت</th>
                                <th class="text-center">سهم خالص فروشنده</th>
                                <th class="text-center">تاریخ ثبت فروش</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td class="text-right font-weight-bold text-secondary">{{ $sale->product?->name ?? 'محصول حذف شده' }}</td>
                                    <td class="text-center"><span class="badge badge-light font-numeric">{{ $sale->order?->order_code ?? '---' }}</span></td>
                                    <td class="text-center font-numeric">{{ number_format($sale->price) }}</td>
                                    <td class="text-center text-danger font-numeric">{{ number_format($sale->site_share) }}</td>
                                    <td class="text-center text-success font-weight-bold font-numeric">{{ number_format($sale->seller_share) }}</td>
                                    <td class="text-center text-muted font-numeric">{{ verta($sale->created_at)->format('Y/m/d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">هیچ دیتای فروشی برای این فروشنده در سیستم ثبت نشده است.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sales->hasPages())
                    <div class="card-footer bg-white d-flex justify-content-center">
                        {{ $sales->appends(request()->except('sales_page'))->links() }}
                    </div>
                @endif
            </div>

            {{-- بخش لیست تسویه حساب‌ها --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="ti-wallet text-success mr-2"></i> تاریخچه تسویه حساب‌های مالی</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center">کد پیگیری شبا / مرجع</th>
                                <th class="text-center">مبلغ تسویه (تومان)</th>
                                <th class="text-center">وضعیت پرداخت</th>
                                <th class="text-center">کارشناس تاییدکننده</th>
                                <th class="text-center">تاریخ و زمان واریز</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($settlements as $settlement)
                                <tr>
                                    <td class="text-center font-numeric"><strong class="text-secondary">{{ $settlement->reference_id ?? '---' }}</strong></td>
                                    <td class="text-center font-weight-bold text-dark font-numeric">{{ number_format($settlement->amount) }}</td>
                                    <td class="text-center">
                                        @if($settlement->status === 'paid')
                                            <span class="badge badge-success px-3 py-2">پرداخت موفق</span>
                                        @else
                                            <span class="badge badge-warning px-3 py-2">در انتظار واریز</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">{{ $settlement->user?->name ?? 'سیستم خودکار' }}</td>
                                    <td class="text-center text-muted font-numeric">
                                        {{ $settlement->paid_at ? verta($settlement->paid_at)->format('Y/m/d H:i') : '---' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">تاکنون هیچ فاکتور تسویه حسابی برای این فروشنده صادر نشده است.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($settlements->hasPages())
                    <div class="card-footer bg-white d-flex justify-content-center">
                        {{ $settlements->appends(request()->except('settlements_page'))->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    <script>
        function copyToClipboard(text, title) {
            if (!text) return;
            let cleanText = text.replace(/[\s-]/g, '');
            navigator.clipboard.writeText(cleanText).then(function() {
                alert(title + ' با موفقیت کپی شد: ' + cleanText);
            }).catch(function(err) {
                console.error('خطا در کپی: ', err);
            });
        }
    </script>
@endsection
