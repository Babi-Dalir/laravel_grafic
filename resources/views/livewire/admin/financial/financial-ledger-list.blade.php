<div class="main-content p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-weight-bold text-dark"><i class="ti-server text-primary mr-2"></i> جعبه سیاه مالی و دفتر کل پلتفرم (Financial Ledger)</h4>
        <span class="badge badge-primary px-3 py-2 font-numeric">بروزرسانی آنی زنده</span>
    </div>

    {{-- کارت‌های تجمیعی هوشمند لجر --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white h-100 border-right border-primary" style="border-right-width: 4px !important;">
                <div class="card-body py-3">
                    <small class="text-muted font-weight-bold">
                        کل گردش مالی (تراکنش‌های موفق)
                        <i class="ti-help-alt text-muted mr-1" data-toggle="tooltip" title="مجموع کل مبالغی که کاربران با کارت بانکی از درگاه پرداخت کرده‌اند"></i>
                    </small>
                    <h4 class="mt-2 mb-0 font-weight-bold font-numeric text-dark">
                        {{ number_format($totals->total_turnover ?? 0) }} <small style="font-size: 12px">تومان</small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white h-100 border-right border-info" style="border-right-width: 4px !important;">
                <div class="card-body py-3">
                    <small class="text-muted font-weight-bold">
                        کارمزد/درآمد ناخالص سایت
                        <i class="ti-help-alt text-muted mr-1" data-toggle="tooltip" title="سهم تئوریک پلتفرم از درصد کمیسیون‌ها پیش از کسر سوبسیدها"></i>
                    </small>
                    <h4 class="mt-2 mb-0 font-weight-bold font-numeric text-info">
                        {{ number_format($totals->total_site_share ?? 0) }} <small style="font-size: 12px">تومان</small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white h-100 border-right border-danger" style="border-right-width: 4px !important;">
                <div class="card-body py-3">
                    <small class="text-muted font-weight-bold">
                        کل سوبسید اعطایی پلتفرم
                        <i class="ti-help-alt text-muted mr-1" data-toggle="tooltip" title="مبالغی که به خاطر تخفیف کمپین‌ها، سایت متعهد شده از سهم خودش به فروشنده پرداخت کند"></i>
                    </small>
                    <h4 class="mt-2 mb-0 font-weight-bold font-numeric text-danger">
                        {{ number_format($totals->total_subsidy ?? 0) }} <small style="font-size: 12px">تومان</small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-dark text-white h-100 border-right border-success" style="border-right-width: 4px !important;">
                <div class="card-body py-3">
                    <small class="text-success font-weight-bold">
                        سود خالص واقعی پلتفرم
                        <i class="ti-help-alt text-success mr-1" data-toggle="tooltip" title="رقم نهایی سود پلتفرم که شامل: (درآمد ناخالص منهای سوبسیدها) است"></i>
                    </small>
                    <h4 class="mt-2 mb-0 font-weight-bold font-numeric text-success">
                        {{ number_format(($totals->total_site_share ?? 0) - ($totals->total_subsidy ?? 0)) }} <small style="font-size: 12px">تومان</small>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- بخش سرچ و فیلتر تب‌ها --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn {{ $activeTab === 'all' ? 'btn-primary' : 'btn-light' }} px-4" wire:click="changeTab('all')">کل تراکنش‌ها</button>
                        <button type="button" class="btn {{ $activeTab === 'market' ? 'btn-primary' : 'btn-light' }} px-4" wire:click="changeTab('market')">محصولات فروشندگان</button>
                        <button type="button" class="btn {{ $activeTab === 'website' ? 'btn-primary' : 'btn-light' }} px-4" wire:click="changeTab('website')">فروش مستقیم سایت</button>
                    </div>
                </div>
                <div class="col-md-6 position-relative">
                    <input type="text" class="form-control text-left" dir="rtl" wire:model.live.debounce.400ms="search" placeholder="جستجو بر اساس نام محصول یا کد سفارش...">
                    <div wire:loading wire:target="search, changeTab" class="spinner-border spinner-border-sm text-primary position-absolute" style="left: 25px; top: 12px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول اصلی دفتر کل --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th class="text-center">ردیف</th>
                        <th class="text-right">مشخصات فاکتور / محصول</th>
                        <th class="text-center">نوع تراکنش</th>
                        <th class="text-center">مبلغ پرداختی کاربر</th>
                        <th class="text-center">سهم ناخالص سایت</th>
                        <th class="text-center">سوبسید پلتفرم</th>
                        <th class="text-center">سهم غرفه‌دار</th>
                        <th class="text-center">تاریخ ثبت دفتری</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $index => $record)
                        @php
                            $isWebsite = $record->seller_share == 0;
                        @endphp
                        <tr wire:key="ledger-row-{{ $record->id }}">
                            <td class="text-center font-numeric font-weight-bold">{{ $records->firstItem() + $index }}</td>
                            <td class="text-right">
                                <span class="text-dark font-weight-bold d-block">{{ $record->product?->name ?? 'محصول حذف شده' }}</span>
                                <small class="text-muted font-numeric">کد سفارش: #{{ $record->order?->order_code ?? '---' }}</small>

                                {{-- 🟢 شفاف‌سازی برای مدیر: نمایش نام غرفه‌دار در لجر --}}
                                @if(!$isWebsite && $record->product?->seller)
                                    <span class="d-block small text-info mt-1">
                                            <i class="ti-user small"></i> غرفه‌دار: {{ $record->product->seller->brand_name ?? 'نامشخص' }}
                                        </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($isWebsite)
                                    <span class="badge badge-primary px-2 py-1" style="font-size: 11px;">فروش مستقیم سایت</span>
                                @else
                                    <span class="badge badge-info px-2 py-1" style="font-size: 11px;">فروش مارکت‌پلیس</span>
                                @endif
                            </td>
                            <td class="text-center font-numeric font-weight-bold text-dark">{{ number_format($record->price) }}</td>
                            <td class="text-center font-numeric text-info">{{ number_format($record->site_share) }}</td>
                            <td class="text-center font-numeric">
                                {{-- 🟢 شفاف‌سازی برای مدیر: استایل‌دهی قرمز به سوبسیدها برای هوشیاری --}}
                                @if($record->platform_subsidy > 0)
                                    <span class="text-danger font-weight-bold">{{ number_format($record->platform_subsidy) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center font-numeric font-weight-bold text-success">
                                {{ $isWebsite ? '---' : number_format($record->seller_share) }}
                            </td>
                            <td class="text-center font-numeric text-muted small">
                                {{ verta($record->created_at)->format('Y/m/d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted bg-light">هیچ تراکنش مالی موفقی با این مشخصات یافت نشد.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($records->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-3">
                {{ $records->links() }}
            </div>
        @endif
    </div>

</div>
