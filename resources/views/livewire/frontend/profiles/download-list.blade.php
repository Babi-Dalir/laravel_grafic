<div class="dt-sl">
    <div class="table-responsive">

        <table class="table table-order text-center align-middle">

            <thead>
            <tr>
                <th>ردیف</th>
                <th>محصول</th>
                <th>تاریخ خرید</th>
                <th>تعداد دانلود</th>
                <th>وضعیت</th>
                <th>دانلود</th>
            </tr>
            </thead>

            <tbody>

            @forelse($downloads as $index => $download)

                @php
                    $remaining = max(0, $download->max_download - $download->download_count);
                @endphp

                <tr>

                    <td>
                        {{ $downloads->firstItem() + $index }}
                    </td>

                    <td>
                        {{ $download->product->name }}
                    </td>

                    <td>
                        {{ verta($download->created_at)->format('Y/m/d') }}
                    </td>

                    <td id="counter-cell-{{ $index }}">
                        {{ $download->download_count }}
                        /
                        {{ $download->max_download }}
                    </td>

                    <td id="status-cell-{{ $index }}">
                        @if($download->download_count >= $download->max_download)
                            <span class="badge badge-danger">
                                سقف دانلود تکمیل شده
                            </span>
                        @else
                            <span class="badge badge-success">
                                قابل دانلود
                            </span>
                        @endif
                    </td>

                    <td id="action-cell-{{ $index }}">
                        @if($download->download_count < $download->max_download)

                            <button type="button"
                                    class="btn btn-success"
                                    data-url="{{ route('download.file', $download->token) }}"
                                    data-remaining="{{ $remaining }}"
                                    data-max="{{ $download->max_download }}"
                                    data-index="{{ $index }}"
                                    onclick="executeSecureDownload(this)">
                                دانلود فایل
                            </button>

                        @else
                            <button class="btn btn-secondary btn-sm" disabled>
                                دانلود غیرفعال
                            </button>
                        @endif
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        <div class="alert alert-warning mb-0">
                            هنوز محصولی خریداری نکرده‌اید.
                        </div>
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-3">
        {{ $downloads->links('vendor.pagination.profile-pagination.profile_downloads') }}
    </div>

</div>
@push('scripts')
    <script>
        function executeSecureDownload(btn) {
            // جلوگیری از کلیک‌های تکراری و اسپم
            if (btn.disabled || btn.style.pointerEvents === 'none') {
                return;
            }

            let downloadUrl = btn.dataset.url;
            let remaining = parseInt(btn.dataset.remaining);
            let maxDownload = btn.dataset.max ?? "5";
            let index = btn.dataset.index;

            // ۱. سناریوی آخرین دانلود مجاز (بار پنجم)
            if (remaining <= 1) {
                // 🟢 قدم اول: فوراً دکمه را کاملاً طوسی و غیرفعال کن (دفع ۱۰۰٪ باگ ۴۰۳)
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
                btn.className = 'btn btn-secondary btn-sm';
                btn.innerHTML = 'دانلود غیرفعال';

                // آپدیت آنی متن تعداد دانلود
                let counterCell = document.getElementById(`counter-cell-${index}`);
                if (counterCell) {
                    counterCell.innerHTML = `${maxDownload} / ${maxDownload}`;
                }

                // تغییر آنلاین وضعیت بج به قرمز
                let statusCell = document.getElementById(`status-cell-${index}`);
                if (statusCell) {
                    statusCell.innerHTML = `<span class="badge badge-danger">سقف دانلود تکمیل شده</span>`;
                }

                // 🟢 قدم دوم: حالا با خیال راحت به مرورگر دستور بده فایل را در پس‌زمینه دانلود کند
                window.location.href = downloadUrl;
                return;
            }

            // ۲. سناریوی دانلودهای معمولی (۱ تا ۴)
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

            // فرمان دانلود برای دفعات میانی
            window.location.href = downloadUrl;

            // آزاد کردن دکمه بعد از ۳ ثانیه برای دانلودهای بعدی
            setTimeout(function () {
                btn.style.pointerEvents = 'auto';
                btn.innerHTML = originalHTML;
            }, 3000);
        }
    </script>
@endpush
