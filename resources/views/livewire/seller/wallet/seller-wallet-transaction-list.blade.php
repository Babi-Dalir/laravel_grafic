<div class="table-responsive">

    {{-- فیلتر --}}
    <div class="row mb-3">

        <div class="col-md-6">
            <input type="text"
                   class="form-control"
                   wire:model.live.debounce.500ms="search"
                   placeholder="جستجو (توضیحات یا کد سفارش)">
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

    {{-- خلاصه --}}
    <div class="row mb-4 justify-content-center text-center">

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>در انتظار تسویه</h6>
                    <h4>{{ number_format($pending) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>تسویه شده</h6>
                    <h4>{{ number_format($paid) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>کل درآمد فروش</h6>
                    <h4 class="text-success">
                        {{ number_format($pending + $paid) }}
                    </h4>
                </div>
            </div>
        </div>

    </div>

    {{-- جدول --}}
    <table class="table table-striped">

        <thead>
        <tr>
            <th>ردیف</th>
            <th>محصول</th>
            <th>مبلغ</th>
            <th>نوع</th>
            <th>سفارش</th>
            <th>توضیحات</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
        </tr>
        </thead>

        <tbody>

        @forelse($transactions as $index => $tx)

            @php
                $orderDetail = $tx->order?->orderDetails?->first();
                $product = $orderDetail?->product;
            @endphp

            <tr>

                {{-- ردیف --}}
                <td>
                    {{ $transactions->firstItem() + $index }}
                </td>

                {{-- محصول --}}
                <td>
                    {{ $product?->name ?? '--' }}
                </td>

                {{-- مبلغ --}}
                <td>
                    @if($tx->type === \App\Enums\TransactionType::Sale->value)
                        <span class="text-success">
                            + {{ number_format($tx->amount) }}
                        </span>
                    @else
                        <span class="text-danger">
                            - {{ number_format($tx->amount) }}
                        </span>
                    @endif
                </td>

                {{-- نوع --}}
                <td>
                    @if($tx->type === \App\Enums\TransactionType::Sale->value)
                        <span class="badge bg-success">فروش</span>
                    @endif
                </td>

                {{-- سفارش --}}
                <td>
                    {{ $tx->order?->order_code ?? '--' }}
                </td>

                {{-- توضیحات --}}
                <td>
                    {{ \Illuminate\Support\Str::limit($tx->description, 20) }}
                </td>

                {{-- وضعیت --}}
                <td>
                    @if($tx->status === 'pending')
                        <span class="badge bg-warning">در انتظار</span>
                    @else
                        <span class="badge bg-success">تسویه شده</span>
                    @endif
                </td>

                {{-- تاریخ --}}
                <td>
                    {{ \Hekmatinasser\Verta\Verta::instance($tx->created_at)->format('%d %B %Y') }}
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    تراکنشی یافت نشد
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{ $transactions->appends(Request::except('page'))->links() }}
    </div>

</div>
