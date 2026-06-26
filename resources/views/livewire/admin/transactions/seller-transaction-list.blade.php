<div class="table overflow-auto" tabindex="8">
    {{-- هدر جستجو --}}
    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold">جستجو (نام، کد ملی، برند، کد سفارش)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="مشخصات تراکنش یا فروشنده را تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    {{-- جدول استاندارد تراکنش‌ها --}}
    <table class="table table-striped table-hover align-middle">
        <thead class="thead-light">
        <tr>
            <th class="text-right">فروشنده / برند</th>
            <th class="text-center">کد ملی</th>
            <th class="text-center">مبلغ تراکنش (ریال)</th>
            <th class="text-center">نوع تراکنش</th>
            <th class="text-center">وضعیت مالی</th>
            <th class="text-center">کد سفارش</th>
            <th class="text-center">تاریخ آزادسازی</th>
            <th class="text-center">تاریخ تسویه شده</th>
        </tr>
        </thead>

        {{-- 🟢 تگ tbody به خارج از حلقه منتقل شد تا ساختار جدول در مرورگر نشکند --}}
        <tbody>
        @forelse($transactions as $tx)
            <tr wire:key="transaction-row-{{ $tx->id }}">
                <td class="text-right">
                    <strong class="text-dark">{{ $tx->seller?->first_name }} {{ $tx->seller?->last_name }}</strong>
                    @if($tx->seller?->brand_name)
                        <br><small class="text-info">برند: {{ $tx->seller->brand_name }}</small>
                    @endif
                </td>

                <td class="text-center font-numeric">
                    {{ $tx->seller?->national_code ?? '---' }}
                </td>

                <td class="text-center font-weight-bold text-success font-numeric">
                    {{ number_format($tx->amount) }}
                </td>

                <td class="text-center">
                    @if($tx->type === \App\Enums\TransactionType::Sale->value)
                        <span class="badge badge-success px-2 py-1">فروش محصول</span>
                    @else
                        <span class="badge badge-secondary px-2 py-1">{{ $tx->type }}</span>
                    @endif
                </td>

                <td class="text-center">
                    @if($tx->status === \App\Enums\WalletTransactionStatus::Pending->value)
                        <span class="badge badge-warning px-2 py-1">
                            <i class="ti-time mr-1"></i> در انتظار تسویه
                        </span>
                    @else
                        <span class="badge badge-success px-2 py-1">
                            <i class="ti-check mr-1"></i> تسویه شده
                        </span>
                    @endif
                </td>

                <td class="text-center">
                    <span class="badge badge-light font-numeric">{{ $tx->order?->order_code ?? '---' }}</span>
                </td>

                <td class="text-center text-muted font-numeric">
                    {{ $tx->release_at ? \Hekmatinasser\Verta\Verta::instance($tx->release_at)->format('%d %B، %Y') : '---' }}
                </td>

                <td class="text-center text-muted font-numeric">
                    {{ $tx->settled_at ? \Hekmatinasser\Verta\Verta::instance($tx->settled_at)->format('%d %B، %Y') : '---' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <div class="empty-state">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h5 class="text-dark" style="font-weight: 600;">تراکنشی یافت نشد!</h5>
                        <p class="text-muted">رکورد تراکمی معتبری با عبارت <strong class="text-danger">"{{ $search }}"</strong> در سیستم پیدا نشد.</p>
                        @if($search)
                            <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="ti-eraser mr-1"></i> پاکسازی جستجو
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- بخش پجینیشن بدون تداخل با سایر روت‌ها --}}
    @if($transactions->hasPages())
        <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
            {{ $transactions->appends(request()->except('page'))->links() }}
        </div>
    @endif
</div>
