@extends('admin.layouts.master')

@section('content')
    <main class="main-content">

        <div id="ajax-alert" class="alert d-none mb-3"></div>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="container">

                    @php
                        // بررسی اینکه آیا فرم باید کاملاً قفل شود یا خیر
                        $isLocked = $seller?->exists && ($seller->status === \App\Enums\SellerStatus::Active->value || $seller->status === \App\Enums\SellerStatus::Pending->value);
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title text-primary mb-0">
                            <i class="ti-id-badge mr-2"></i> احراز هویت فروشنده
                        </h5>

                        {{-- نمایش وضعیت فعلی حساب بانکی --}}
                        @if($seller?->bank_verified)
                            <span class="badge badge-success p-2">
                                <i class="ti-check-box mr-1"></i> حساب بانکی تایید شده
                            </span>
                        @elseif($seller?->exists)
                            <span class="badge badge-warning p-2">
                                <i class="ti-time mr-1"></i> در انتظار بررسی / غیرفعال
                            </span>
                        @endif
                    </div>

                    <form action="{{ route('store.seller.verification') }}" method="POST" id="seller-verification-form">
                        @csrf

                        <h6 class="text-secondary mb-3 font-weight-bold">اطلاعات فردی</h6>

                        {{-- first_name --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="first_name"
                                       class="form-control @if($isLocked) locked-input @endif"
                                       value="{{ old('first_name', $seller?->first_name) }}"
                                       @if($isLocked) disabled @endif>
                                <small class="text-danger" id="error-first_name"></small>
                            </div>
                        </div>

                        {{-- last_name --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام خانوادگی <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="last_name"
                                       class="form-control @if($isLocked) locked-input @endif"
                                       value="{{ old('last_name', $seller?->last_name) }}"
                                       @if($isLocked) disabled @endif>
                                <small class="text-danger" id="error-last_name"></small>
                            </div>
                        </div>

                        {{-- brand_name --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام برند (اختیاری)</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="brand_name"
                                       class="form-control @if($isLocked) locked-input @endif"
                                       value="{{ old('brand_name', $seller?->brand_name) }}"
                                       @if($isLocked) disabled @endif>
                                <small class="text-danger" id="error-brand_name"></small>
                            </div>
                        </div>

                        {{-- national_code --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">کد ملی <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="national_code"
                                       maxlength="10"
                                       class="form-control text-left font-numeric @if($isLocked) locked-input @endif"
                                       dir="ltr"
                                       value="{{ old('national_code', $seller?->national_code) }}"
                                       @if($isLocked) disabled @endif>
                                <small class="text-danger" id="error-national_code"></small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-info mb-3 font-weight-bold"><i class="ti-credit-card mr-1"></i> اطلاعات حساب بانکی جهت تسویه‌حساب</h6>

                        {{-- card_number --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">شماره کارت <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="card_number"
                                       maxlength="16"
                                       class="form-control text-left font-numeric @if($isLocked) locked-input @endif"
                                       dir="ltr"
                                       value="{{ old('card_number', $seller?->card_number) }}"
                                       @if($isLocked) disabled @endif>
                                <small class="text-danger" id="error-card_number"></small>
                            </div>
                        </div>

                        {{-- account_number --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">شماره حساب <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="account_number"
                                       class="form-control text-left font-numeric @if($isLocked) locked-input @endif"
                                       dir="ltr"
                                       value="{{ old('account_number', $seller?->account_number) }}"
                                       @if($isLocked) disabled @endif>
                                <small class="text-danger" id="error-account_number"></small>
                            </div>
                        </div>

                        {{-- iban --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">شماره شبا (IBAN) <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group" dir="ltr">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted">IR</span>
                                    </div>
                                    <input type="text"
                                           name="iban"
                                           class="form-control text-left font-numeric @if($isLocked) locked-input @endif"
                                           placeholder="120120000000001234567890"
                                           value="{{ old('iban', str_replace('IR', '', $seller?->iban)) }}"
                                           @if($isLocked) disabled @endif>
                                </div>
                                <small class="text-danger" id="error-iban"></small>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Warning & Status Boxes --}}
                        @if($seller?->status === \App\Enums\SellerStatus::Active->value)
                            <div class="alert alert-success border-0 shadow-xs">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="ti-check mr-2 font-size-18"></i>
                                    <strong>وضعیت حساب: احراز هویت تایید شده است</strong>
                                </div>
                                <p class="mb-0 text-muted" style="font-size: 13px;">
                                    اطلاعات شما مورد تایید مدیریت قرار گرفته است. جهت هرگونه تغییر یا به‌روزرسانی در اطلاعات فردی و بانکی، لطفاً با <strong class="text-dark">پشتیبانی تلگرام</strong> یا مرکز تماس ارتباط برقرار کنید.
                                </p>
                            </div>
                        @elseif($seller?->status === \App\Enums\SellerStatus::Pending->value)
                            <div class="alert alert-info border-0 shadow-xs">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="ti-time mr-2 font-size-18"></i>
                                    <strong>وضعیت حساب: در حال بررسی توسط مدیریت</strong>
                                </div>
                                <p class="mb-0 text-muted" style="font-size: 13px;">
                                    اطلاعات شما ارسال شده و در حال بررسی است. در صورت نیاز به اصلاح فوری مدارک، می‌توانید به آیدی تلگرام پشتیبانی پیام دهید.
                                </p>
                            </div>
                        @elseif($seller?->status === \App\Enums\SellerStatus::Rejected->value && $seller->national_code)
                            <div class="alert alert-danger border-0 shadow-xs">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="ti-close mr-2 font-size-18"></i>
                                    <strong>وضعیت حساب: عدم تایید اطلاعات</strong>
                                </div>
                                <p class="mb-0 text-muted" style="font-size: 13px;">
                                    اطلاعات احراز هویت شما مورد تایید قرار نگرفت. لطفاً موارد را اصلاح کرده و مجدداً ارسال کنید. در صورت ابهام با پشتیبانی در تماس باشید.
                                </p>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 shadow-xs">
                                <i class="ti-alert mr-2"></i>
                                <strong>توجه:</strong> پس از ثبت اطلاعات، فرم قفل شده و جهت ویرایش‌های بعدی باید با پشتیبانی هماهنگ کنید.
                            </div>
                        @endif

                        {{-- باکس راهنمای ارتباط با پشتیبانی (در حالت قفل نمایش داده می‌شود) --}}
                        @if($isLocked)
                            <div class="bg-light p-3 rounded border mt-3 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="ti-headphone-alt text-primary font-size-20 mr-2"></i>
                                    <span class="text-dark font-weight-bold" style="font-size: 13px;">
                                        نیازمند ویرایش اطلاعات قفل‌شده هستید؟
                                    </span>
                                </div>
                                <div>
                                    <a href="https://t.me/your_admin_username" target="_blank" class="btn btn-sm btn-outline-primary mr-2">
                                        <i class="ti-location-arrow mr-1"></i> پیام به پشتیبانی تلگرام
                                    </a>
                                    <a href="tel:02112345678" class="btn btn-sm btn-outline-secondary">
                                        <i class="ti-mobile mr-1"></i> تماس با پشتیبانی
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Submit Button --}}
                        <div class="form-group row mt-4">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" id="submit-verification-btn"
                                        class="btn btn-success btn-uppercase px-4"
                                        @if($isLocked) disabled @endif>
                                    <i class="ti-save mr-1"></i>
                                    <span class="btn-text">ثبت و ارسال اطلاعات</span>
                                </button>
                            </div>
                        </div>

                    </form>

                </div>

            </div>
        </div>

    </main>

    {{-- استایل اختصاصی جهت زیباتر شدن فیلدهای قفل شده --}}
    <style>
        .locked-input {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
            cursor: not-allowed !important;
            border-color: #e9ecef !important;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const $form = $('#seller-verification-form');
            const $submitBtn = $('#submit-verification-btn');
            const $alert = $('#ajax-alert');

            @if(!$isLocked)
            // ۱. اعتبارسنجی آنی فیلدها هنگام خارج شدن از فیلد (Blur)
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
                const fieldName = $(this).attr('name');
                $('#error-' + fieldName).text('');
            });

            // ۲. ثبت نهایی فرم به صورت Ajax
            $form.on('submit', function (e) {
                e.preventDefault();

                $('small.text-danger').text('');
                $alert.addClass('d-none').removeClass('alert-success alert-danger').text('');

                $submitBtn.attr('disabled', true);
                $submitBtn.find('i').removeClass('ti-save').addClass('ti-reload spinner-border spinner-border-sm');
                $submitBtn.find('.btn-text').text('در حال ارسال اطلاعات...');

                const formData = new FormData(this);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $alert.removeClass('d-none alert-danger').addClass('alert-success').text(response.message || "اطلاعات با موفقیت ثبت شد و به وضعیت در حال بررسی تغییر یافت.");
                        $('html, body').animate({ scrollTop: 0 }, 'slow');

                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function (xhr) {
                        $submitBtn.removeAttr('disabled');
                        $submitBtn.find('i').removeClass('ti-reload spinner-border spinner-border-sm').addClass('ti-save');
                        $submitBtn.find('.btn-text').text('ثبت و ارسال اطلاعات');

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
            @endif
        });
    </script>
@endpush
