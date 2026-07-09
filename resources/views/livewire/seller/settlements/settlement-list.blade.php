<div class="table overflow-auto" tabindex="8">

    {{-- نمایش آلرت موفقیت آمیز تغییر وضعیت مالی --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show text-right mb-4" role="alert">
            <i class="ti-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- نمایش آلرت خطای احتمالی همپوشانی رکوئست‌ها --}}
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show text-right mb-4" role="alert">
            <i class="ti-alert mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- فیلتر جستجو --}}
    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">جستجو (فروشنده / نام برند)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="نام فروشنده یا برند مورد نظر را بنویسید...">

            <div wire:loading wire:target="search" class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    {{-- جدول اطلاعات تسویه --}}
    <table class="table table-striped table-hover align-middle">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">مشخصات فروشنده</th>
            <th class="text-center align-middle text-primary">نام برند</th>
            <th class="text-center align-middle text-primary">مبلغ تسویه (تومان)</th>
            <th class="text-center align-middle text-primary">وضعیت فاکتور</th>
            <th class="text-center align-middle text-primary">شناسه پیگیری / مرجع</th>
            <th class="text-center align-middle text-primary">تاییدکننده / واریزکننده</th>
            <th class="text-center align-middle text-primary">تاریخ پرداخت</th>
            <th class="text-center align-middle text-primary">عملیات ادمین</th>
            <th class="text-center align-middle text-primary">تاریخ درخواست</th>
        </tr>
        </thead>

        <tbody>
        @forelse($settlements as $index => $settlement)
            <tr wire:key="settlement-row-{{ $settlement->id }}">
                {{-- ردیف --}}
                <td class="text-center align-middle font-numeric">
                    {{ $settlements->firstItem() + $index }}
                </td>

                {{-- فروشنده --}}
                <td class="text-right align-middle">
                    <div>
                        <strong class="text-dark">
                            {{ $settlement->seller?->first_name ?? '---' }}
                            {{ $settlement->seller?->last_name ?? '' }}
                        </strong>
                        <br>
                        <small class="text-muted font-numeric">
                            <i class="ti-mobile mr-1"></i> {{ $settlement->seller?->user?->mobile ?? 'بدون موبایل' }}
                        </small>
                    </div>
                </td>

                {{-- برند --}}
                <td class="text-center align-middle text-info font-weight-bold">
                    {{ $settlement->seller?->brand_name ?? '---' }}
                </td>

                {{-- مبلغ --}}
                <td class="text-center align-middle font-weight-bold text-success font-numeric">
                    {{ number_format($settlement->amount) }} <small style="font-size: 11px">تومان</small>
                </td>

                {{-- وضعیت فاکتور تسویه (سازگار با مقدار انوم و رشته خام دیتابیس) --}}
                <td class="text-center align-middle">
                    @if($settlement->status === \App\Enums\SettlementStatus::Pending->value || $settlement->status === \App\Enums\SettlementStatus::Pending)
                        <span class="badge badge-warning px-2 py-1">
                            <i class="ti-time mr-1"></i> در انتظار پرداخت
                        </span>
                    @elseif($settlement->status === \App\Enums\SettlementStatus::Paid->value || $settlement->status === \App\Enums\SettlementStatus::Paid)
                        <span class="badge badge-success px-2 py-1">
                            <i class="ti-check mr-1"></i> پرداخت شده
                        </span>
                    @else
                        <span class="badge badge-secondary px-2 py-1">
                            {{ $settlement->status }}
                        </span>
                    @endif
                </td>

                {{-- شماره مرجع پیگیری --}}
                <td class="text-center align-middle text-muted font-numeric">
                    <small>{{ $settlement->reference_id ?? '---' }}</small>
                </td>

                {{-- پرداخت‌کننده --}}
                <td class="text-center align-middle text-secondary">
                    {{ $settlement->user?->name ?? 'سیستم خودکار' }}
                </td>

                {{-- تاریخ پرداخت --}}
                <td class="text-center align-middle text-muted font-numeric">
                    @if($settlement->paid_at)
                        {{ \Hekmatinasser\Verta\Verta::instance($settlement->paid_at)->format('%d %B، %Y') }}
                    @else
                        <span class="text-light-muted">---</span>
                    @endif
                </td>

                {{-- عملیات ادمین همراه با لایه ضد دبل‌کلیک مخرب مالی --}}
                <td class="text-center align-middle">
                    @if($settlement->status === \App\Enums\SettlementStatus::Pending->value || $settlement->status === \App\Enums\SettlementStatus::Pending)
                        <button
                            wire:click="pay({{ $settlement->id }})"
                            wire:loading.attr="disabled"
                            wire:target="pay({{ $settlement->id }})"
                            class="btn btn-success btn-sm px-3 shadow-xs">

                            {{-- نمایش لودر حین فرستادن اطلاعات مالی به دیتابیس --}}
                            <span wire:loading.remove wire:target="pay({{ $settlement->id }})">
                                <i class="ti-wallet mr-1"></i> ثبت پرداخت
                            </span>
                            <span wire:loading wire:target="pay({{ $settlement->id }})">
                                <i class="fa fa-spinner fa-spin mr-1"></i> در حال واریز...
                            </span>
                        </button>
                    @else
                        <span class="text-muted font-weight-bold"><i class="ti-check-box text-success mr-1"></i> تکمیل شده</span>
                    @endif
                </td>

                {{-- تاریخ ایجاد درخواست --}}
                <td class="text-center align-middle text-muted font-numeric">
                    {{ \Hekmatinasser\Verta\Verta::instance($settlement->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-5 text-muted">
                    <div class="empty-state">
                        <i class="ti-info-alt text-light mb-2 d-block" style="font-size: 2rem;"></i>
                        فاکتور تسویه حسابی منطبق با جستجوی شما یافت نشد.
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- ساختار پجینیشن بهینه شده بدون اختلال دوقلو --}}
    @if($settlements->hasPages())
        <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
            {{ $settlements->appends(request()->except('page'))->links() }}
        </div>
    @endif

</div>
