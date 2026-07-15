@extends('frontend.layouts.master')

@section('content')

    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-3">
        <div class="container main-container">

            <div class="row">

                {{-- Sidebar --}}
                @include('frontend.profile.sidebar')

                {{-- Content --}}
                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

                    <!-- باکس نمایش پیام‌های موفقیت یا خطا با اژاکس -->
                    <div id="ajax-alert" class="alert d-none mb-3"></div>

                    @if(session()->has('message'))
                        <div class="alert alert-info mb-3">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="profile-form-card">

                        <div class="profile-form-header">
                            <h4>درخواست همکاری به عنوان طراح</h4>
                            <p>
                                اطلاعات خود را تکمیل کنید تا درخواست همکاری شما بررسی شود.
                            </p>
                        </div>

                        <form id="seller-request-form"
                              action="{{ route('profile.store.request.seller') }}"
                              method="POST"
                              enctype="multipart/form-data">

                            @csrf

                            {{-- وضعیت درخواست --}}
                            @if($sellerRequest)

                                @if($sellerRequest->status === \App\Enums\SellerRequestStatus::Pending->value)

                                    <div class="request-status-card status-warning mb-4">

                                        <div class="status-icon">
                                            <i class="mdi mdi-timer-sand"></i>
                                        </div>

                                        <div>
                                            <h6>در انتظار بررسی</h6>
                                            <p>درخواست شما توسط مدیریت در حال بررسی است.</p>
                                        </div>

                                    </div>

                                @elseif($sellerRequest->status === \App\Enums\SellerRequestStatus::Approved->value)

                                    <div class="request-status-card status-success mb-4">

                                        <div class="status-icon">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                        </div>

                                        <div>
                                            <h6>درخواست تایید شده</h6>
                                            <p>شما به عنوان طراح تایید شده‌اید.</p>
                                        </div>

                                    </div>

                                @elseif($sellerRequest->status === \App\Enums\SellerRequestStatus::Rejected->value)

                                    <div class="request-status-card status-danger mb-4">

                                        <div class="status-icon">
                                            <i class="mdi mdi-close-circle-outline"></i>
                                        </div>

                                        <div>
                                            <h6>درخواست رد شده</h6>
                                            <p>{{ $sellerRequest->admin_note }}</p>
                                        </div>

                                    </div>

                                @endif

                            @endif

                            <div class="row">

                                {{-- نام برند --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        نام برند یا نام هنری
                                    </label>

                                    <input type="text"
                                           class="form-control input-ui"
                                           name="brand_name"
                                           value="{{ old('brand_name',$sellerRequest?->brand_name) }}"
                                           placeholder="مثال: Graphic Master">

                                    <small class="text-danger" id="error-brand_name"></small>

                                </div>

                                {{-- نمونه کار --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        لینک نمونه کار
                                    </label>

                                    <input type="url"
                                           class="form-control input-ui"
                                           name="portfolio"
                                           value="{{ old('portfolio',$sellerRequest?->portfolio) }}"
                                           placeholder="https://behance.net/...">

                                    <small class="text-danger" id="error-portfolio"></small>

                                </div>

                                {{-- توضیحات --}}
                                <div class="col-12 mb-3">

                                    <label class="form-label">
                                        معرفی خود و سوابق طراحی
                                    </label>

                                    <textarea
                                        name="reason"
                                        rows="6"
                                        class="form-control input-ui"
                                        placeholder="خودتان را معرفی کنید و درباره تخصص‌ها و تجربیات خود توضیح دهید...">{{ old('reason',$sellerRequest?->reason) }}</textarea>

                                    <small class="text-danger" id="error-reason"></small>

                                </div>

                                {{-- رزومه --}}
                                <div class="col-12 mb-3">

                                    <label class="form-label">
                                        رزومه (اختیاری)
                                    </label>

                                    <input type="file"
                                           name="resume"
                                           class="form-control input-ui">

                                    <small class="text-danger" id="error-resume"></small>

                                </div>

                            </div>

                            {{-- پیام‌های مدیریتی --}}

                            @if(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Rejected->value &&
                                $sellerRequest->admin_note
                            )

                                <div class="alert alert-danger mt-3">

                                    درخواست شما در تاریخ

                                    {{ \Hekmatinasser\Verta\Verta::instance($sellerRequest->reviewed_at)->format('%d %B، %Y') }}

                                    بررسی شد و به دلیل

                                    <strong>{{ $sellerRequest->admin_note }}</strong>

                                    رد شده است.

                                </div>

                            @elseif(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Approved->value
                            )

                                <div class="alert alert-success mt-3">

                                    درخواست شما در تاریخ

                                    {{ \Hekmatinasser\Verta\Verta::instance($sellerRequest->reviewed_at)->format('%d %B، %Y') }}

                                    تایید شده است و اکنون می‌توانید محصولات و طرح‌های خود را ثبت نمایید.

                                </div>

                            @elseif(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Pending->value
                            )

                                <div class="alert alert-warning mt-3">
                                    درخواست شما در حال بررسی است.
                                </div>

                            @endif

                            {{-- احراز هویت --}}
                            @if($sellerRequest && $sellerRequest->status === \App\Enums\SellerRequestStatus::Approved->value)

                                <div class="verification-card mt-4">

                                    <div class="verification-content">

                                        <div class="verification-icon">
                                            <i class="mdi mdi-shield-check-outline"></i>
                                        </div>

                                        <div>
                                            <h5>احراز هویت فروشنده</h5>

                                            <p>
                                                برای فعال سازی کامل حساب فروشندگی،
                                                احراز هویت خود را تکمیل کنید.
                                            </p>
                                        </div>

                                    </div>

                                    <a href="{{ route('profile.verification.seller') }}"
                                       class="btn-save d-inline-block text-center text-white"
                                       style="background: #fff; color: #4f46e5 !important; box-shadow: none;">
                                        شروع احراز هویت
                                    </a>

                                </div>

                            @elseif(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Pending->value
                            )

                                <div class="form-actions">

                                    <button type="button"
                                            class="btn-save btn-disabled"
                                            disabled>

                                        <i class="mdi mdi-clock-outline"></i>

                                        درخواست شما در حال بررسی است

                                    </button>

                                </div>

                            @else

                                <div class="form-actions">

                                    <button type="submit"
                                            id="submit-btn"
                                            class="btn-save">

                                        <i class="mdi mdi-account-check-outline"></i>

                                        <span>ارسال درخواست همکاری</span>

                                    </button>

                                </div>

                            @endif

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </main>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const $form = $('#seller-request-form');
            const $submitBtn = $('#submit-btn');
            const $alert = $('#ajax-alert');

            // ۱. اعتبارسنجی آنی فیلدها هنگام خارج شدن از فیلد (Blur)
            // ۱. اعتبارسنجی آنی فیلدها هنگام خارج شدن از فیلد (Blur) + پاک کردن ارور هنگام تایپ (Input)
            $form.find('input, textarea').not('[type="file"]').on('blur', function () {
                const $input = $(this);
                const fieldName = $input.attr('name');

                if (!fieldName || fieldName === '_token') return;

                const formData = new FormData();
                formData.append(fieldName, $input.val());
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('only_validate', fieldName);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function () {
                        $('#error-' + fieldName).text('');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors && errors[fieldName]) {
                                $('#error-' + fieldName).text(errors[fieldName][0]);
                            }
                        }
                    }
                });
            }).on('input', function() {
                // 🟢 به محض اینکه کاربر شروع به تایپ کرد، ارور قرمز فوراً پاک می‌شود
                const fieldName = $(this).attr('name');
                $('#error-' + fieldName).text('');
            });

            // ۲. ثبت نهایی فرم به صورت Ajax
            $form.on('submit', function (e) {
                e.preventDefault();

                // ریست کردن خطاها و الرت‌ها
                $('small.text-danger').text('');
                $alert.addClass('d-none').removeClass('alert-success alert-danger').text('');

                // غیرفعال کردن دکمه و قرار دادن حالت لودینگ
                $submitBtn.attr('disabled', true);
                $submitBtn.find('span').text('در حال ارسال درخواست...');

                const formData = new FormData(this);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $alert.removeClass('d-none alert-danger').addClass('alert-success').text(response.message || "درخواست شما با موفقیت ثبت شد و در حال بررسی است.");
                        $('html, body').animate({ scrollTop: 0 }, 'slow');

                        // رفرش بعد از ۲ ثانیه برای بروزرسانی وضعیت نمایش تاییدیه یا وضعیت انتظار در صفحه
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function (xhr) {
                        $submitBtn.removeAttr('disabled');
                        $submitBtn.find('span').text('ارسال درخواست همکاری');

                        $alert.removeClass('d-none alert-success').addClass('alert-danger');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            $alert.text("لطفاً خطاهای فرم را برطرف نمایید.");
                        } else {
                            $alert.text("خطایی در ثبت اطلاعات رخ داد. لطفاً مجدداً تلاش کنید.");
                        }

                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    }
                });
            });
        });
    </script>
@endpush
