<div class="table overflow-auto" tabindex="8">
    <div class="form-group row align-items-center mb-4">
        <label class="col-sm-2 col-form-label font-weight-bold">عنوان جستجو محصول:</label>
        <div class="col-sm-10 position-relative">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="نام محصول را جهت فیلتر تایپ کنید...">
            <div wire:loading wire:target="search"
                 class="spinner-border spinner-border-sm text-primary position-absolute"
                 style="left: 25px; top: 12px;"></div>
        </div>
    </div>

    <table class="table table-striped table-hover bg-white border">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">نام فروشنده</th>
            <th class="text-center align-middle text-primary">نام محصول</th>
            <th class="text-center align-middle text-primary">قیمت</th>
            <th class="text-center align-middle text-primary">تخفیف</th>
            <th class="text-center align-middle text-primary">درصد کمیسیون</th>
            <th class="text-center align-middle text-primary">سهم سایت</th>
            <th class="text-center align-middle text-primary">سهم فروشنده</th>
            <th class="text-center align-middle text-primary">وضعیت و عملیات</th>
            <th class="text-center align-middle text-primary">تعداد دانلود</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($order_details as $index => $order_detail)
            <tr>
                <td class="text-center align-middle font-weight-bold">{{ $order_details->firstItem() + $index }}</td>
                <td class="text-center align-middle">
                    <div>
                        <strong>
                            {{ $order_detail->product?->user?->name ?? 'بدون فروشنده/حذف شده' }}
                        </strong>

                        <br>

                        <small class="text-muted">
                            {{ $order_detail->product?->user?->mobile }}
                        </small>

                    </div>
                </td>
                <td class="text-center align-middle font-weight-bold">
                    {{ $order_detail->product?->name }}
                    @if($order_detail->product?->trashed())
                        <span class="badge badge-danger small">حذف شده</span>
                    @endif
                </td>
                <td class="text-center align-middle text-success font-weight-bold">{{ number_format($order_detail->price) }}
                    تومان
                </td>
                <td class="text-center align-middle text-danger">{{ number_format($order_detail->discount) }} تومان</td>
                <td class="text-center align-middle text-secondary">
                    {{ number_format($order_detail->product?->category?->commission?->commission_percent ?? 20) }} %
                </td>
                <td class="text-center align-middle text-info">{{ number_format($order_detail->site_share) }} تومان</td>
                <td class="text-center align-middle text-primary font-weight-bold">{{ number_format($order_detail->seller_share) }}
                    تومان
                </td>

                {{-- مدیریت وضعیت بر پایه دکمه‌های تفکیک شده دراپ‌داون --}}
                <td class="text-center align-middle">
                    <div class="btn-group">
                        @if($order_detail->status === \App\Enums\OrderDetailStatus::Waiting)
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                در حال انتظار
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-success"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'pay')"><i
                                        class="ti-check ml-1"></i> تغییر به پرداخت شده
                                </button>
                                <button class="dropdown-item text-danger"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'refund')"><i
                                        class="ti-back-left ml-1"></i> مرجوع کردن آیتم
                                </button>
                            </div>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Paid)
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                پرداخت شده
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-info"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'download')"><i
                                        class="ti-download ml-1"></i> تغییر به دانلود شده
                                </button>
                                <button class="dropdown-item text-danger"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'refund')"><i
                                        class="ti-back-left ml-1"></i> مرجوع کردن آیتم
                                </button>
                            </div>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Downloaded)
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                کاملا دانلود شده
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-danger"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'refund')"><i
                                        class="ti-back-left ml-1"></i> مرجوع کردن آیتم
                                </button>
                            </div>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Refunded)
                            <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                مرجوع شده
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-warning"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'reset')"><i
                                        class="ti-reload ml-1"></i> بازگردانی به حالت انتظار
                                </button>
                            </div>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle font-weight-bold text-secondary">
                    {{ $order_detail->download?->download_count ?? 0 }}
                    / {{ $order_detail->download?->max_download ?? 0 }}
                </td>
                <td class="text-center align-middle text-muted small">
                    {{ \Hekmatinasser\Verta\Verta::instance($order_detail->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center py-5 bg-light text-muted">
                    هیچ جزئیاتی برای این سفارش یافت نشد.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{ $order_details->appends(Request::except('page'))->links() }}
    </div>
</div>
