<div class="table-responsive">

    {{-- فیلترها و دکمه‌های جستجو --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 mb-2 mb-md-0">
            <div class="input-group">
                <input type="text"
                       class="form-control text-right"
                       wire:model.live.debounce.500ms="search"
                       placeholder="جستجو بر اساس توضیحات، کد تراکنش یا کد مرجع...">
                <div class="input-group-append" wire:loading wire:target="search">
                    <span class="input-group-text bg-white border-left-0">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <select class="form-control" wire:model.live="type">
                <option value="">همه انواع تراکنش‌ها</option>
                @foreach($types as $t)
                    <option value="{{ $t->value }}">
                        {{ method_exists($t, 'label') ? $t->label() : $t->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- کارت‌های آمار خلاصه وضعیت کیف پول --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm bg-warning-light text-center py-2">
                <div class="card-body">
                    <h6 class="text-muted font-weight-bold">در انتظار تسویه</h6>
                    <h4 class="text-warning font-weight-bold mt-2 font-numeric">{{ number_format($pending) }} <small>ریال</small></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm bg-success-light text-center py-2">
                <div class="card-body">
                    <h6 class="text-muted font-weight-bold">تسویه شده</h6>
                    <h4 class="text-success font-weight-bold mt-2 font-numeric">{{ number_format($paid) }} <small>ریال</small></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info-light text-center py-2">
                <div class="card-body">
                    <h6 class="text-muted font-weight-bold">کل درآمد حاصل از فروش</h6>
                    <h4 class="text-info font-weight-bold mt-2 font-numeric">{{ number_format($pending + $paid) }} <small>ریال</small></h4>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول اطلاعات تراکنش‌ها --}}
    <table class="table table-striped table-hover align-middle border">
        <thead class="thead-light">
        <tr>
            <th class="text-center">ردیف</th>
            <th class="text-right">محصول</th>
            <th class="text-center">مبلغ تراکنش</th>
            <th class="text-center">نوع</th>
            <th class="text-center">کد سفارش</th>
            <th class="text-right">توضیحات</th>
            <th class="text-center">وضعیت مالی</th>
            <th class="text-center">تاریخ ثبت</th>
        </tr>
        </thead>

        <tbody>
        @forelse($transactions as $index => $tx)
            @php
                // واکشی امن از روی ریلیشن لود شده بدون اجرای کوئری سنگین تکراری
                $orderDetail = $tx->order?->orderDetails?->first();
                $product = $orderDetail?->product;

                // بررسی وضعیت تراکنش با انوم یا رشته خام
                $isSale = ($tx->type === \App\Enums\TransactionType::Sale->value || $tx->type === \App\Enums\TransactionType::Sale);
            @endphp
            <tr wire:key="transaction-row-{{ $tx->id }}">
                {{-- ردیف --}}
                <td class="text-center align-middle font-numeric">
                    {{ $transactions->firstItem() + $index }}
                </td>

                {{-- نام محصول --}}
                <td class="text-right align-middle text-dark font-weight-bold">
                    {{ $product?->name ?? '--' }}
                </td>

                {{-- مبلغ --}}
                <td class="text-center align-middle font-weight-bold font-numeric">
                    @if($isSale)
                        <span class="text-success">+ {{ number_format($tx->amount) }}</span>
                    @else
                        <span class="text-danger">- {{ number_format($tx->amount) }}</span>
                    @endif
                </td>

                {{-- نوع تراکنش --}}
                <td class="text-center align-middle">
                    @if($isSale)
                        <span class="badge badge-success px-2 py-1">فروش محصول</span>
                    @else
                        <span class="badge badge-danger px-2 py-1">برداشت / تسویه</span>
                    @endif
                </td>

                {{-- کد فاکتور سفارش --}}
                <td class="text-center align-middle font-numeric text-secondary">
                    {{ $tx->order?->order_code ?? $tx->code ?? '--' }}
                </td>

                {{-- توضیحات تراکنش --}}
                <td class="text-right align-middle text-muted" title="{{ $tx->description }}">
                    {{ \Illuminate\Support\Str::limit($tx->description, 35) }}
                </td>

                {{-- وضعیت تراکنش کیف پول --}}
                <td class="text-center align-middle">
                    @if($tx->status === 'pending')
                        <span class="badge badge-warning px-2 py-1"><i class="ti-time mr-1"></i> در انتظار</span>
                    @else
                        <span class="badge badge-success px-2 py-1"><i class="ti-check mr-1"></i> تسویه شده</span>
                    @endif
                </td>

                {{-- تاریخ شمسی سیستم --}}
                <td class="text-center align-middle font-numeric text-muted">
                    {{ \Hekmatinasser\Verta\Verta::instance($tx->created_at)->format('%d %B %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <div class="empty-state">
                        <i class="ti-face-sad text-light d-block mb-2" style="font-size: 2rem;"></i>
                        هیچ تراکنش مالی برای این فیلتر یا عبارت جستجو ثبت نشده است.
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- پجینیشن بهینه لایووایر ۳ --}}
    @if($transactions->hasPages())
        <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
            {{ $transactions->appends(request()->except('page'))->links() }}
        </div>
    @endif

</div>
