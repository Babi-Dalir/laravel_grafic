<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (نام و نام خانواگی، کدملی فروشنده، برند، کد سفارش)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    <table class="table table-striped">

        <thead>
        <tr>
            <th>فروشنده</th>
            <th>کد ملی</th>
            <th>مبلغ</th>
            <th>نوع</th>
            <th>وضعیت</th>
            <th>سفارش</th>
            <th>آزادسازی</th>
            <th>تسویه</th>
        </tr>
        </thead>

        <tbody>

        @forelse($transactions as $tx)

            <tr>

                <td>
                    {{ $tx->seller->first_name }}
                    {{ $tx->seller->last_name }}
                </td>

                <td>
                    {{ $tx->seller?->national_code ?? '---' }}
                </td>

                <td>
                    {{ number_format($tx->amount) }}
                </td>

                <td>
                    @if($tx->type === \App\Enums\TransactionType::Sale->value)
                        <span class="badge bg-success">
                        فروش
                    </span>
                    @endif
                </td>

                <td>

                    @if($tx->status === \App\Enums\WalletTransactionStatus::Pending->value)

                        <span class="badge bg-warning">
                        در انتظار تسویه
                    </span>

                    @else

                        <span class="badge bg-success">
                        تسویه شده
                    </span>

                    @endif

                </td>

                <td>
                    {{ $tx->order?->order_code ?? '---' }}
                </td>

                <td>
                    {{$tx->release_at ? \Hekmatinasser\Verta\Verta::instance($tx->release_at)->format('%d%B، %Y') : '---'}}
                </td>

                <td>
                    {{ $tx->settled_at
                        ? \Hekmatinasser\Verta\Verta::instance($tx->settled_at)->format('%d %B، %Y')
                        : '---'
                    }}
                </td>

            </tr>

        </tbody>
        @empty
            <tr>
                <td colspan="9" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        {{-- یک SVG ساده و شیک برای حالت جستجو --}}
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <small>
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </small>

                            <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                            <p class="text-muted">کاربری با عبارت <strong class="text-danger">"{{ $search }}"</strong>
                                در سیستم ثبت نشده است.</p>

                            @if($search)
                                <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="ti-eraser m-r-5"></i> پاکسازی جستجو
                                </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse

    </table>

    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$transactions->appends(Request::except('page'))->links()}}
    </div>
</div>

