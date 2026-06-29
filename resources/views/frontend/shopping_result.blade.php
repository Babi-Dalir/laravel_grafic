@extends('frontend.layouts.master')
@section('content')
    <div class="wrapper shopping-page pb-5">

        <div class="premium-steps-container">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <ul class="premium-checkout-steps">
                            <li class="active">
                                <div class="step-capsule">
                                    <i class="mdi mdi-truck-delivery-outline"></i>
                                    <span>اطلاعات ارسال</span>
                                </div>
                            </li>
                            <li class="step-divider"><i class="mdi mdi-chevron-left"></i></li>
                            <li class="active">
                                <div class="step-capsule">
                                    <i class="mdi mdi-credit-card-outline"></i>
                                    <span>پرداخت آنلاین</span>
                                </div>
                            </li>
                            <li class="step-divider"><i class="mdi mdi-chevron-left"></i></li>
                            <li class="active">
                                <div class="step-capsule">
                                    <i class="mdi mdi-check-all"></i>
                                    <span>اتمام خرید</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-12">

                    @if($result == "success")
                        <div class="checkout-card">

                            <div class="status-alert-box success-mode">
                                <div class="status-alert-icon success-mode">
                                    <i class="mdi mdi-check-bold"></i>
                                </div>
                                <div class="status-alert-title">
                                    <h4>سفارش شما با موفقیت ثبت و تایید شد</h4>
                                    <p>کد یکتای پیگیری سفارش شما: <strong
                                            class="text-dark">{{ $order->order_code }}</strong></p>
                                </div>
                            </div>

                            <div class="row align-items-center mb-5">
                                <div class="col-md-8 col-12">
                                    <p class="text-muted small mb-md-0 mb-3" style="line-height: 1.7;">
                                        تراکنش مالی با موفقیت تایید شد. این سفارش هم‌اکنون در وضعیت <span
                                            class="badge-status success">تکمیل شده</span> قرار دارد. لایسنس‌ها یا
                                        فایل‌های خریداری شده در پایین همین صفحه آماده دانلود هستند.
                                    </p>
                                </div>
                                <div class="col-md-4 col-12 text-md-left">
                                    <a href="{{ route('profile.orders') }}" class="btn-modern btn-modern-secondary btn-block">
                                        <i class="mdi mdi-text-box-search-outline"></i>
                                        پیگیری در پنل کاربری
                                    </a>
                                </div>
                            </div>

                            <div class="factor-premium-grid mb-5">
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-account-outline"></i></div>
                                    <div class="item-content">
                                        <span class="label">تحویل‌گیرنده</span>
                                        <span class="value">{{ $order->user->name }}</span>
                                    </div>
                                </div>
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-phone-outline"></i></div>
                                    <div class="item-content">
                                        <span class="label">شماره تماس</span>
                                        <span class="value">{{ $order->user->mobile }}</span>
                                    </div>
                                </div>
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-cash-check text-success"></i></div>
                                    <div class="item-content">
                                        <span class="label">مبلغ کل پرداختی</span>
                                        <span class="value text-success" style="font-weight: 700;">{{ number_format($order->total_price) }} تومان</span>
                                    </div>
                                </div>
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-credit-card-check-outline"></i></div>
                                    <div class="item-content">
                                        <span class="label">شیوه پرداخت</span>
                                        <span class="value">درگاه آنلاین شتاب</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <h5 class="font-weight-bold mb-4 text-dark" style="font-size: 15px;">
                                    <i class="mdi mdi-download-box-outline text-success ml-1"></i>
                                    فایل‌های آماده دانلود مستقیم
                                </h5>

                                @forelse($downloads ?? collect() as $download)

                                    @php
                                        $canDownload = method_exists($download, 'canDownload')
                                            ? $download->canDownload()
                                            : ($download->download_count < $download->max_download);

                                        $remaining = max(0, $download->max_download - $download->download_count);
                                    @endphp

                                    <div
                                        class="download-row d-flex flex-wrap align-items-center justify-content-between p-3 mb-3"
                                        style="background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;">

                                        <div class="d-flex flex-column gap-1">

                                            <h6 class="font-weight-bold text-dark mb-2" style="font-size: 14px;">
                                                {{ $download->product->name }}
                                            </h6>

                                            <div class="d-flex align-items-center flex-wrap gap-2">

                    <span class="text-muted small ml-3">
                        <i class="mdi mdi-refresh ml-1"></i>
                        {{ $download->download_count }} از {{ $download->max_download }} دانلود
                    </span>

                                                @if($canDownload)
                                                    <span class="badge-status success small"
                                                          style="padding: 3px 10px; font-size: 11px;">
                            {{ $remaining }} دانلود باقی‌مانده
                        </span>
                                                @else
                                                    <span class="badge-status failed small"
                                                          style="padding: 3px 10px; font-size: 11px;">
                            سقف دانلود تکمیل شده
                        </span>
                                                @endif

                                            </div>
                                        </div>

                                        <div class="mt-3 mt-sm-0">

                                            @if($canDownload)
                                                <a href="{{ route('download.file', $download->token) }}"
                                                   class="btn-modern btn-modern-success download-btn"
                                                   data-token="{{ $download->token }}"
                                                   data-remaining="{{ $remaining }}"
                                                   data-max="{{ $download->max_download }}"
                                                   {{-- 🟢 اضافه شدن دیتاست ماکسیمم --}}
                                                   onclick="handleDownload(this)">
                                                    <i class="mdi mdi-download"></i>
                                                    دانلود مستقیم فایل
                                                </a>
                                            @else
                                                <button class="btn-modern btn-modern-secondary"
                                                        disabled
                                                        style="opacity:.6;cursor:not-allowed;">
                                                    <i class="mdi mdi-download-off"></i>
                                                    دانلود غیرفعال
                                                </button>
                                            @endif

                                        </div>

                                    </div>

                                @empty
                                    <div class="alert alert-warning border-0 p-3 rounded-xl small">
                                        <i class="mdi mdi-alert-circle-outline ml-1"></i>
                                        فایل دانلودی برای این سفارش یافت نشد.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    @else
                        <div class="checkout-card">

                            <div class="status-alert-box failed-mode">
                                <div class="status-alert-icon failed-mode">
                                    <i class="mdi mdi-close"></i>
                                </div>
                                <div class="status-alert-title">
                                    <h4>عملیات پرداخت ناموفق بود یا توسط کاربر لغو شد</h4>
                                    <p>کد سفارش معلق: <strong>{{ $order?->order_code }}</strong></p>
                                </div>
                            </div>

                            <div class="alert alert-danger border-0 small p-3 rounded-xl mb-4"
                                 style="background: #fff5f5; color: #e11d48; line-height: 1.6;">
                                <i class="mdi mdi-information-outline ml-1"></i>
                                جهت جلوگیری از لغو خودکار سفارش توسط سیستم، لطفاً ظرف ۱ ساعت آینده نسبت به پرداخت اقدام
                                نمایید. مبالغ احتمالی کسر شده توسط بانک، ظرف ۷۲ ساعت عودت داده می‌شوند.
                            </div>

                            <div class="factor-premium-grid mb-5">
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-account-outline"></i></div>
                                    <div class="item-content">
                                        <span class="label">تحویل‌گیرنده</span>
                                        <span class="value">{{ $order?->user->name }}</span>
                                    </div>
                                </div>
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-phone-outline"></i></div>
                                    <div class="item-content">
                                        <span class="label">شماره تماس</span>
                                        <span class="value">{{ $order?->user->mobile }}</span>
                                    </div>
                                </div>
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-cash-remove text-danger"></i></div>
                                    <div class="item-content">
                                        <span class="label">مبلغ کل فاکتور</span>
                                        <span class="value text-danger" style="font-weight: 700;">{{ number_format($order?->total_price) }} تومان</span>
                                    </div>
                                </div>
                                <div class="factor-premium-item">
                                    <div class="item-icon"><i class="mdi mdi-clock-outline"></i></div>
                                    <div class="item-content">
                                        <span class="label">وضعیت نهایی</span>
                                        <span class="value badge-status failed">در انتظار پرداخت</span>
                                    </div>
                                </div>
                            </div>

                            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 14.5px;">انتخاب مجدد درگاه
                                شتاب و پرداخت</h5>
                            <form method="post" id="shipping-data-form">
                                @csrf
                                <div class="payment-methods-grid">
                                    <div class="payment-method-card selected" onclick="selectPaymentCard(this)">
                                        <i class="mdi mdi-credit-card-outline text-primary"
                                           style="font-size: 26px;"></i>
                                        <div>
                                            <div class="font-weight-bold text-dark small">پرداخت آنلاین هوشمند (کارت‌های
                                                عضو شتاب)
                                            </div>
                                            <span class="text-muted" style="font-size: 11px;">اتصال ایمن به تمام درگاه‌های بانکی رسمی کشور</span>
                                        </div>
                                        <input type="radio" name="payment_type" id="gateway_zarinpal" value="zarinpal"
                                               checked style="position: absolute; left: 24px;">
                                    </div>
                                </div>

                                <div class="text-left mt-4">
                                    <button type="submit" class="btn-modern btn-modern-primary px-5">
                                        <i class="mdi mdi-credit-card-check-outline"></i>
                                        تلاش مجدد برای پرداخت آنلاین
                                    </button>
                                </div>
                            </form>

                            <div class="mt-5">
                                <h5 class="font-weight-bold mb-3 text-muted" style="font-size: 13.5px;">تاریخچه تلاش‌های
                                    این تراکنش</h5>
                                <div class="table-responsive"
                                     style="border: 1px solid #eef2f6; border-radius: 20px; overflow: hidden; background: #ffffff;">
                                    <table class="modern-table">
                                        <thead>
                                        <tr>
                                            <th>ردیف</th>
                                            <th>درگاه ارجاعی</th>
                                            <th>شناسه سفارش</th>
                                            <th>مبلغ تراکنش</th>
                                            <th>وضعیت ثبت شده</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>۱</td>
                                            <td>شتابیت (آنلاین)</td>
                                            <td>{{ $order?->order_code }}</td>
                                            <td class="font-weight-bold">{{ number_format($order?->total_price) }}
                                                تومان
                                            </td>
                                            <td><span class="badge-status failed">ناموفق / لغو شده</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <footer class="mini-footer mt-5">
            <div class="container">
                <div class="row text-muted small py-4" style="border-top: 1px solid #e2e8f0;">
                    <div class="col-md-6 text-md-right text-center mb-md-0 mb-2">
                        <i class="mdi mdi-phone-classic ml-1"></i> پشتیبانی تلفنی بازارچه: ۰۲۱-۶۱۹۳۰۰۰۰
                    </div>
                    <div class="col-md-6 text-md-left text-center">
                        <i class="mdi mdi-shield-check-outline ml-1"></i> پرداخت ایمن شتابی تحت نظارت شاپرک
                    </div>
                </div>
            </div>
        </footer>
    </div>
@endsection


@push('scripts')
    <script>
        function selectPaymentCard(element) {
            document.querySelectorAll('.payment-method-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            element.querySelector('input[type="radio"]').checked = true;
        }

        function handleDownload(btn) {
            if (btn.classList.contains('disabled') || btn.style.pointerEvents === 'none') {
                return;
            }

            let remaining = parseInt(btn.dataset.remaining);
            let downloadRow = btn.closest('.download-row');
            let maxDownload = btn.dataset.max ?? "5";

            // ۱. سناریوی آخرین دانلود مجاز (بار پنجم)
            if (remaining <= 1) {
                // اعمال تاخیر ایمن ۵۰ میلی‌ثانیه‌ای برای استارت دانلود و قفل فوری دکمه
                setTimeout(function () {
                    btn.classList.remove('btn-modern-success');
                    btn.classList.add('btn-modern-secondary');
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '.6';
                    btn.style.cursor = 'not-allowed';
                    btn.removeAttribute('href');
                    btn.innerHTML = '<i class="mdi mdi-download-off"></i> دانلود غیرفعال (سقف تکمیل)';

                    // آپدیت وضعیت به سقف تکمیل شده
                    let badge = downloadRow.querySelector('.badge-status');
                    if (badge) {
                        badge.classList.remove('success');
                        badge.classList.add('failed');
                        badge.innerText = 'سقف دانلود تکمیل شده';
                    }

                    // آپدیت متن شمارنده متنی
                    let counterSpan = downloadRow.querySelector('.text-muted.small');
                    if (counterSpan) {
                        counterSpan.innerHTML = `<i class="mdi mdi-refresh ml-1"></i> ${maxDownload} از ${maxDownload} دانلود`;
                    }
                }, 50); // 🟢 هماهنگ شده با منطق پنل کاربری جهت دفع رکوئست دوبل

                return;
            }

            // ۲. سناریویی که هنوز دانلود باقی مانده است (دانلودهای ۱ تا ۴)
            btn.style.pointerEvents = 'none';
            let originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> در حال دانلود...';

            let newRemaining = remaining - 1;
            btn.dataset.remaining = newRemaining;

            let badge = downloadRow.querySelector('.badge-status.success');
            if (badge) {
                badge.innerText = `${newRemaining} دانلود باقی‌مانده`;
            }

            // آپدیت موقت متنی شمارنده برای دانلودهای میانی
            let counterSpan = downloadRow.querySelector('.text-muted.small');
            if (counterSpan) {
                let currentDownload = parseInt(maxDownload) - newRemaining;
                counterSpan.innerHTML = `<i class="mdi mdi-refresh ml-1"></i> ${currentDownload} از ${maxDownload} دانلود`;
            }

            setTimeout(function () {
                btn.style.pointerEvents = 'auto';
                btn.innerHTML = originalHTML;
            }, 3000);
        }
    </script>
@endpush
