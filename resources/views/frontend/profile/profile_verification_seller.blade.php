@extends('frontend.layouts.master')

@section('content')
    @include('frontend.layouts.header')
    <main class="main-content dt-sl mb-3">

        <div class="container main-container">

            <div class="row">

                @include('frontend.profile.sidebar')

                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

                    <div id="ajax-alert" class="alert d-none mb-3"></div>

                    {{-- Header Card --}}
                    <div class="verification-header mb-4 p-4 rounded-lg shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="verification-icon mr-3">
                                <i class="mdi mdi-account-check-outline"></i>
                            </div>
                            <div>
                                <h4 class="mb-1">احراز هویت فروشنده</h4>
                                <small class="text-muted">
                                    برای فعال‌سازی فروشندگی، اطلاعات خود را تکمیل کنید
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Form Card --}}
                    <div class="profile-form-card p-4 shadow-sm rounded-lg">

                        @php
                            // بررسی اینکه آیا فرم باید کاملاً قفل شود یا خیر
                            $isLocked = $seller->exists && ($seller->status === \App\Enums\SellerStatus::Active->value || $seller->status === \App\Enums\SellerStatus::Pending->value);
                        @endphp

                        <form action="{{ route('store.seller.verification') }}" method="POST" id="seller-verification-form">
                            @csrf

                            {{-- Personal Info --}}
                            <h5 class="section-title mb-3">اطلاعات فردی</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نام</label>
                                    <input type="text" name="first_name" required
                                           class="form-control input-ui @if($isLocked) locked-input @endif"
                                           value="{{ old('first_name', $seller?->first_name) }}"
                                           @if($isLocked) disabled @endif>
                                    <small class="text-danger" id="error-first_name"></small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>نام خانوادگی</label>
                                    <input type="text" name="last_name" required
                                           class="form-control input-ui @if($isLocked) locked-input @endif"
                                           value="{{ old('last_name', $seller?->last_name) }}"
                                           @if($isLocked) disabled @endif>
                                    <small class="text-danger" id="error-last_name"></small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>نام برند (اختیاری)</label>
                                    <input type="text" name="brand_name"
                                           class="form-control input-ui @if($isLocked) locked-input @endif"
                                           value="{{ old('brand_name', $seller?->brand_name) }}"
                                           @if($isLocked) disabled @endif>
                                    <small class="text-danger" id="error-brand_name"></small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>کد ملی</label>
                                    <input type="text" name="national_code" required
                                           maxlength="10"
                                           class="form-control input-ui text-left @if($isLocked) locked-input @endif"
                                           dir="ltr"
                                           value="{{ old('national_code', $seller?->national_code) }}"
                                           @if($isLocked) disabled @endif>
                                    <small class="text-danger" id="error-national_code"></small>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Bank Info --}}
                            <h5 class="section-title mb-3 text-info">اطلاعات بانکی</h5>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>شماره کارت</label>
                                    <input type="text" name="card_number" required
                                           maxlength="16"
                                           class="form-control input-ui text-left @if($isLocked) locked-input @endif"
                                           dir="ltr"
                                           value="{{ old('card_number', $seller?->card_number) }}"
                                           @if($isLocked) disabled @endif>
                                    <small class="text-danger" id="error-card_number"></small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>شماره حساب</label>
                                    <input type="text" name="account_number" required
                                           class="form-control input-ui text-left @if($isLocked) locked-input @endif"
                                           dir="ltr"
                                           value="{{ old('account_number', $seller?->account_number) }}"
                                           @if($isLocked) disabled @endif>
                                    <small class="text-danger" id="error-account_number"></small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>شماره شبا</label>
                                    <div class="input-group" dir="ltr">
                                        <span class="input-group-text bg-light text-muted border-left-0">IR</span>
                                        <input type="text" name="iban" required
                                               class="form-control input-ui text-left @if($isLocked) locked-input @endif"
                                               placeholder="مثال: 120120000000001234567890"
                                               style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                                               value="{{ old('iban', str_replace('IR', '', $seller?->iban)) }}"
                                               @if($isLocked) disabled @endif>
                                    </div>
                                    <small class="text-danger" id="error-iban"></small>
                                </div>
                            </div>

                            {{-- Warning Box --}}
                            @if($seller?->status === \App\Enums\SellerStatus::Active->value)
                                <div class="alert alert-success mt-4">
                                    <i class="mdi mdi-check-circle-outline ml-1"></i>
                                    احراز هویت شما تکمیل است و مورد تایید مدیر قرار گرفته است.
                                </div>
                            @elseif($seller?->status === \App\Enums\SellerStatus::Pending->value)
                                <div class="alert alert-info mt-4">
                                    <i class="mdi mdi-clock-outline ml-1"></i>
                                    اطلاعات شما ارسال شده و در حال بررسی توسط مدیریت است. تا زمان اعلام نتیجه امکان ارسال مجدد وجود ندارد.
                                </div>
                            @elseif($seller?->status === \App\Enums\SellerStatus::Rejected->value && $seller->national_code)
                                <div class="alert alert-danger mt-4">
                                    <i class="mdi mdi-close-circle-outline ml-1"></i>
                                    اطلاعات احراز هویت شما مورد تایید قرار نگرفت. لطفاً اطلاعات خود را اصلاح و مجدداً ارسال کنید.
                                </div>
                            @else
                                <div class="alert alert-warning mt-4">
                                    <strong>توجه:</strong>
                                    پس از ثبت اطلاعات، درخواست شما بررسی خواهد شد و امکان ویرایش تا زمان بررسی مدیریت محدود می‌شود.
                                </div>
                            @endif

                            {{-- Submit --}}
                            <div class="text-left mt-4">
                                <button type="submit" id="submit-verification-btn"
                                        class="btn btn-primary btn-lg px-5"
                                        @if($isLocked) disabled @endif>
                                    <i class="mdi mdi-content-save-outline ml-1"></i>
                                    <span class="btn-text">ثبت و ارسال اطلاعات</span>
                                </button>
                            </div>

                        </form>

                    </div>

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

            // اگر فرم قفل بود، نیازی به فعال کردن اعتبارسنجی آنی نیست
            @if(!$isLocked)
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
                const fieldName = $(this).attr('name');
                $('#error-' + fieldName).text('');
            });

            // ۲. ثبت نهایی فرم به صورت Ajax بدون رفرش ناگهانی
            $form.on('submit', function (e) {
                e.preventDefault();

                $('small.text-danger').text('');
                $alert.addClass('d-none').removeClass('alert-success alert-danger').text('');

                $submitBtn.attr('disabled', true);
                $submitBtn.find('i').removeClass('mdi mdi-content-save-outline').addClass('mdi mdi-refresh mdi-spin');
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
                        $submitBtn.find('i').removeClass('mdi mdi-refresh mdi-spin').addClass('mdi mdi-content-save-outline');
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
