<div class="dt-sl">
    <div class="table-responsive">
        <table class="table table-order text-center align-middle">
            <thead>
            <tr>
                <th>ردیف</th>
                <th>نام محصول</th>
                <th>مبلغ پرداخت شده</th>
                <th>تخفیف</th>
                <th>وضعیت</th>
                <th>تعداد دانلود</th>
                <th>دانلود</th>
            </tr>
            </thead>
            <tbody>
            @forelse($order_details as $index => $order_detail)
                @php
                    // محاسبه باقی‌مانده مجاز دانلود به صورت امن
                    $hasDownload = !empty($order_detail->download);
                    $remaining = $hasDownload ? max(0, $order_detail->download->max_download - $order_detail->download->download_count) : 0;
                @endphp
                <tr>
                    <td>{{ $order_details->firstItem() + $index }}</td>
                    <td>{{ $order_detail->product?->name ?? 'محصول حذف شده' }}</td>
                    <td>{{ number_format($order_detail->price) }} تومان</td>
                    <td>{{ number_format($order_detail->discount) }} تومان</td>

                    {{-- 🟢 پچ آیدی برای تغییر آنلاین وضعیت ردیف سفارش --}}
                    <td id="status-cell-{{ $index }}">
                        @if($order_detail->status === \App\Enums\OrderDetailStatus::Paid)
                            <span class="badge badge-success">پرداخت شده</span>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Downloaded || ($hasDownload && $order_detail->download->download_count >= $order_detail->download->max_download))
                            <span class="badge badge-info">کاملا دانلود شده</span>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Refunded)
                            <span class="badge badge-danger">مرجوع شده</span>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Waiting)
                            <span class="badge badge-warning">در حال انتظار</span>
                        @endif
                    </td>

                    {{-- 🟢 پچ آیدی برای شمارنده زنده تعداد دانلود --}}
                    <td id="counter-cell-{{ $index }}">
                        @if($hasDownload)
                            {{ $order_detail->download->download_count }} / {{ $order_detail->download->max_download }}
                        @else
                            -
                        @endif
                    </td>

                    {{-- 🟢 پچ اکشن دکمه دانلود امن و هوشمند --}}
                    <td id="action-cell-{{ $index }}">
                        @if($hasDownload)
                            @if($order_detail->download->download_count < $order_detail->download->max_download)
                                <button type="button"
                                        class="btn btn-success"
                                        data-url="{{ route('download.file', $order_detail->download->token) }}"
                                        data-remaining="{{ $remaining }}"
                                        data-max="{{ $order_detail->download->max_download }}"
                                        data-index="{{ $index }}"
                                        onclick="executeSecureDownload(this)">
                                    دانلود فایل
                                </button>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>
                                    دانلود غیرفعال
                                </button>
                            @endif
                        @else
                            <span class="text-muted text-sm">فاقد فایل دانلود</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="alert alert-warning mb-0">
                            آیتمی برای این سفارش یافت نشد.
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $order_details->links('vendor.pagination.profile-pagination.profile_order_details') }}
    </div>
</div>

@push('scripts')
    <script>
        function executeSecureDownload(btn) {
            if (btn.disabled || btn.style.pointerEvents === 'none') {
                return;
            }

            let downloadUrl = btn.dataset.url;
            let remaining = parseInt(btn.dataset.remaining);
            let maxDownload = btn.dataset.max ?? "5";
            let index = btn.dataset.index;

            // ۱. سناریوی آخرین دانلود مجاز (رسیدن به سقف دانلود)
            if (remaining <= 1) {
                // غیرفعال‌سازی آنی دکمه دانلود
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
                btn.className = 'btn btn-secondary btn-sm';
                btn.innerHTML = 'دانلود غیرفعال';

                // آپدیت آنی متن شمارنده ردیف
                let counterCell = document.getElementById(`counter-cell-${index}`);
                if (counterCell) {
                    counterCell.innerHTML = `${maxDownload} / ${maxDownload}`;
                }

                // 🟢 تغییر آنلاین بج وضعیت سفارش به وضعیت "کاملا دانلود شده" مطابق انوم
                let statusCell = document.getElementById(`status-cell-${index}`);
                if (statusCell) {
                    statusCell.innerHTML = `<span class="badge badge-info">کاملا دانلود شده</span>`;
                }

                window.location.href = downloadUrl;
                return;
            }

            // ۲. سناریوی دانلودهای معمولی (میانی)
            btn.style.pointerEvents = 'none';
            let originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> در حال دانلود...';

            let newRemaining = remaining - 1;
            btn.dataset.remaining = newRemaining;

            let counterCell = document.getElementById(`counter-cell-${index}`);
            if (counterCell) {
                let currentDownload = parseInt(maxDownload) - newRemaining;
                counterCell.innerHTML = `${currentDownload} / ${maxDownload}`;
            }

            window.location.href = downloadUrl;

            setTimeout(function () {
                btn.style.pointerEvents = 'auto';
                btn.innerHTML = originalHTML;
            }, 3000);
        }
    </script>
@endpush
