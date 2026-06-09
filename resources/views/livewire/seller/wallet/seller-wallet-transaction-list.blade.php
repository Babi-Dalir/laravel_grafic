<div class="table-responsive">

    {{-- فیلترها --}}
    <div class="row mb-3">

        <div class="col-md-6">
            <input type="text"
                   class="form-control"
                   wire:model.live.debounce.500ms="search"
                   placeholder="جستجو در توضیحات">
        </div>

        <div class="col-md-6">
            <select class="form-control" wire:model.live="type">

                <option value="">همه نوع‌ها</option>

                @foreach($types as $t)
                    <option value="{{ $t->value }}">
                        {{ $t->label() }}
                    </option>
                @endforeach

            </select>
        </div>

    </div>
    <div class="row mb-4">

        <div class="col-md-4">
            <h6>در انتظار تسویه</h6>
            <h4>{{ number_format($pending) }}</h4>
        </div>

        <div class="col-md-4">
            <h6>تسویه شده</h6>
            <h4>{{ number_format($settled) }}</h4>
        </div>

        <div class="col-md-4">
            <h6>کل درآمد</h6>
            <h4>{{ number_format($pending + $settled) }}</h4>
        </div>

    </div>

    <div class="alert alert-info text-center">

        @if($canWithdraw)
            ✔ شما در دوره تسویه هستید و مبلغ شما در تسویه بعدی آزاد خواهد شد
        @else
            ⏳ تسویه بعدی هنوز فعال نشده (حداقل 30 روز + 100 هزار تومان)
        @endif

    </div>
    {{-- جدول --}}
    <table class="table table-striped table-hover">

        <thead>
        <tr>
            <th class="text-center">ردیف</th>
            <th class="text-center">مبلغ</th>
            <th class="text-center">نوع</th>
            <th class="text-center">توضیحات</th>
            <th class="text-center">کد سفارش</th>
            <th class="text-center">تاریخ</th>
        </tr>
        </thead>

        <tbody>

        @forelse($transactions as $index => $tx)

            <tr>

                <td class="text-center">
                    {{ $transactions->firstItem() + $index }}
                </td>

                <td class="text-center">

                    @php
                        $isNegative = in_array($tx->type, [
                            \App\Enums\TransactionType::Withdrawal->value,
                            \App\Enums\TransactionType::Refund->value,
                            \App\Enums\TransactionType::Commission->value,
                        ]);
                    @endphp

                    @if($isNegative)
                        <span class="text-danger">
            - {{ number_format($tx->amount) }}
        </span>
                    @else
                        <span class="text-success">
            + {{ number_format($tx->amount) }}
        </span>
                    @endif

                </td>

                <td class="text-center">

                    @switch($tx->type)

                        @case(\App\Enums\TransactionType::Sale->value)
                            <span class="badge bg-success">فروش</span>
                            @break

                        @case(\App\Enums\TransactionType::Withdrawal->value)
                            <span class="badge bg-danger">برداشت</span>
                            @break

                        @case(\App\Enums\TransactionType::Refund->value)
                            <span class="badge bg-warning">بازگشت وجه</span>
                            @break

                        @case(\App\Enums\TransactionType::Commission->value)
                            <span class="badge bg-dark">کمیسیون</span>
                            @break

                        @case(\App\Enums\TransactionType::Adjustment->value)
                            <span class="badge bg-info">اصلاح</span>
                            @break

                        @default
                            <span class="badge bg-secondary">{{ $tx->type }}</span>
                    @endswitch

                </td>

                <td class="text-center">

                    <span title="{{ $tx->description }}">
                        {{ \Illuminate\Support\Str::limit($tx->description,40) }}
                    </span>

                </td>

                <td class="text-center">
                    {{ $tx->order?->order_code ?? '--' }}
                </td>

                <td class="text-center">
                    {{\Hekmatinasser\Verta\Verta::instance($tx->created_at)->format('%d%B، %Y')}}
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    تراکنشی یافت نشد
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="mt-3">
        {{ $transactions->appends(Request::except('page'))->links() }}
    </div>

</div>
