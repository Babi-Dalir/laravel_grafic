<div class="table overflow-auto" tabindex="8">

    {{-- سرچ --}}
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (فروشنده / برند)</label>

        <div class="col-sm-10 d-flex align-items-center">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="نام فروشنده یا برند...">

            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    {{-- جدول --}}
    <table class="table table-striped table-hover">

        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">فروشنده</th>
            <th class="text-center align-middle text-primary">برند</th>
            <th class="text-center align-middle text-primary">مبلغ</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary">شناسه</th>
            <th class="text-center align-middle text-primary">پرداخت توسط</th>
            <th class="text-center align-middle text-primary">تاریخ پرداخت</th>
            <th class="text-center align-middle text-primary">عملیات</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>

        <tbody>

        @forelse($settlements as $index => $settlement)

            <tr>

                {{-- ردیف --}}
                <td class="text-center align-middle">
                    {{ $settlements->firstItem() + $index }}
                </td>

                {{-- فروشنده --}}
                <td class="text-center align-middle">
                    <div>
                        <strong>
                            {{ $settlement->seller->first_name ?? '---' }}
                            {{ $settlement->seller->last_name ?? '' }}
                        </strong>

                        <br>

                        <small class="text-muted">
                            #{{ $settlement->seller->id }}
                        </small>
                    </div>
                </td>

                {{-- برند --}}
                <td class="text-center align-middle">
                    {{ $settlement->seller->brand_name ?? '---' }}
                </td>

                {{-- مبلغ --}}
                <td class="text-center align-middle">
                    <span class="text-success">
                        {{ number_format($settlement->amount) }}
                    </span>
                </td>

                {{-- وضعیت --}}
                <td class="text-center align-middle">

                    @if($settlement->status === \App\Enums\SettlementStatus::Pending->value)

                        <span class="badge bg-warning">
                            در انتظار پرداخت
                        </span>

                    @elseif($settlement->status === \App\Enums\SettlementStatus::Paid->value)

                        <span class="badge bg-success">
                            پرداخت شده
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            {{ $settlement->status }}
                        </span>

                    @endif

                </td>

                {{-- reference --}}
                <td class="text-center align-middle">
                    <small>
                        {{ $settlement->reference_id }}
                    </small>
                </td>

                {{-- پرداخت‌کننده --}}
                <td class="text-center align-middle">
                    {{ $settlement->admin?->name ?? '---' }}
                </td>

                {{-- تاریخ پرداخت --}}
                <td class="text-center align-middle">
                    @if($settlement->paid_at)
                        {{ \Hekmatinasser\Verta\Verta::instance($settlement->paid_at)->format('%d %B، %Y') }}
                    @else
                        ---
                    @endif
                </td>

                {{-- عملیات --}}
                <td class="text-center align-middle">

                    @if($settlement->status === \App\Enums\SettlementStatus::Pending->value)

                        <button
                            wire:click="pay({{ $settlement->id }})"
                            class="btn btn-success btn-sm">

                            ثبت پرداخت
                        </button>

                    @else
                        <span class="text-success">تکمیل شده</span>
                    @endif

                </td>

                {{-- تاریخ ایجاد --}}
                <td class="text-center align-middle">
                    {{ \Hekmatinasser\Verta\Verta::instance($settlement->created_at)->format('%d %B، %Y') }}
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="10" class="text-center py-5 text-muted">
                    تسویه‌ای یافت نشد
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

    {{-- pagination --}}
    <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{ $settlements->appends(request()->except('page'))->links() }}
    </div>

</div>
