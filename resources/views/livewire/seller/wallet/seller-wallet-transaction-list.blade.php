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

    {{-- جدول --}}
    <table class="table table-striped table-hover">

        <thead>
        <tr>
            <th class="text-center">ردیف</th>
            <th class="text-center">مبلغ</th>
            <th class="text-center">نوع</th>
            <th class="text-center">توضیحات</th>
            <th class="text-center">مرجع</th>
            <th class="text-center">موجودی بعد</th>
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
                    {{ number_format($tx->amount) }}
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
                    {{ $tx->description ?? '--' }}
                </td>

                <td class="text-center">
                    {{ $tx->reference_id ?? '--' }}
                </td>

                <td class="text-center">
                    {{ number_format($tx->balance_after) }}
                </td>

                <td class="text-center">
                    {{ $tx->created_at->format('Y-m-d H:i') }}
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
        {{ $transactions->links() }}
    </div>

</div>
