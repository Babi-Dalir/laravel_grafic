@extends('frontend.auth.layouts.master')

@section('content')

    @if(session()->has('error'))
        <div class="babi-auth-alert mb-3 text-center">
            <i class="fad fa-exclamation-triangle ml-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session()->has('success'))
        <div class="babi-auth-success mb-3 text-center">
            <i class="fad fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <main class="main-content dt-sl mt-5 mb-5">
        <div class="container main-container">
            <div class="row">

                <div class="col-xl-4 col-lg-5 col-md-7 col-12 mx-auto">

                    <div class="logo-area text-center mb-4">
                        <a href="{{ route('home') }}">
                            <img
                                src="{{ url('frontend/img/logo.png') }}"
                                class="img-fluid babi-auth-logo"
                                alt="logo"
                            >
                        </a>
                    </div>

                    <div class="babi-auth-wrapper">

                        <div class="babi-auth-header mb-4 text-center">

                            <h2 class="babi-auth-title">
                                تایید شماره همراه
                            </h2>

                            <p class="babi-auth-subtitle mt-2">
                                کد ۵ رقمی ارسال شده را وارد نمایید
                            </p>

                        </div>

                        @if(session()->has('message'))
                            <div class="babi-auth-alert mb-3 text-center">
                                <i class="fad fa-exclamation-triangle ml-2"></i>
                                {{ session('message') }}
                            </div>
                        @endif

                        @if(session()->has('success_message'))
                            <div class="babi-auth-success mb-3 text-center">
                                <i class="fad fa-check-circle ml-2"></i>
                                {{ session('success_message') }}
                            </div>
                        @endif

                        <div class="babi-mobile-info-box d-flex align-items-center justify-content-between mb-4">

                            <div class="d-flex align-items-center">

                                <i class="fad fa-mobile-android text-primary ml-2 font-size-20"></i>

                                <span class="babi-phone-num" dir="ltr">
                                    {{ session('register.mobile') ?? '---' }}
                                </span>

                            </div>

                            <a
                                href="{{ route('register') }}"
                                class="babi-edit-phone-btn"
                            >
                                <i class="fal fa-edit ml-1"></i>
                                ویرایش شماره
                            </a>

                        </div>

                        <form
                            action="{{ route('verify.code') }}"
                            method="POST"
                            id="otp-form"
                            autocomplete="off"
                        >

                            @csrf

                            <div class="form-row justify-content-center mb-4">

                                <div class="babi-otp-container" dir="ltr">

                                    @for($i = 0; $i < 5; $i++)

                                        <input
                                            name="code[]"
                                            type="tel"
                                            maxlength="1"
                                            class="babi-otp-input"
                                            inputmode="numeric"
                                            pattern="[0-9]"
                                            autocomplete="one-time-code"
                                            aria-label="رقم {{ $i + 1 }} کد تایید"
                                            required
                                        >

                                    @endfor

                                </div>

                            </div>

                            <div class="form-row justify-content-center mb-4">

                                <div class="babi-timer-wrapper text-center">

                                    <div
                                        id="babi-countdown-container"
                                        class="d-flex align-items-center justify-content-center"
                                    >

                                        <i class="fal fa-clock ml-2 text-muted"></i>

                                        <span class="text-muted ml-1">
                                            ارسال مجدد کد پس از:
                                        </span>

                                        <span
                                            id="countdown-verify-end"
                                            class="text-muted"
                                            dir="ltr"
                                        >
                                            02:00
                                        </span>

                                    </div>

                                    <button
                                        type="button"
                                        id="babi-resend-btn"
                                        class="btn babi-resend-link"
                                        style="display:none;"
                                    >
                                        دریافت مجدد کد تایید
                                    </button>

                                </div>

                            </div>

                            <div class="form-row">

                                <button
                                    type="submit"
                                    id="otp-submit-btn"
                                    class="babi-btn-primary mx-auto w-100"
                                >
                                    تایید و ادامه مسیر
                                    <i class="fad fa-arrow-left mr-2"></i>
                                </button>

                            </div>

                        </form>

                        <div class="babi-auth-footer mt-4 text-center">

                            <span class="text-muted">
                                کاربر جدید هستید؟
                            </span>

                            <a
                                href="{{ route('register') }}"
                                class="babi-redirect-link mr-1"
                            >
                                ثبت نام در سایت
                            </a>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </main>

@endsection


@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const inputs = Array.from(
                document.querySelectorAll('.babi-otp-input')
            );

            const form = document.getElementById('otp-form');

            const submitBtn = document.getElementById('otp-submit-btn');

            const resendBtn = document.getElementById('babi-resend-btn');

            const countdownContainer = document.getElementById(
                'babi-countdown-container'
            );

            const timerDisplay = document.getElementById(
                'countdown-verify-end'
            );


            /*
            |--------------------------------------------------------------------------
            | تبدیل اعداد فارسی و عربی به انگلیسی
            |--------------------------------------------------------------------------
            */

            function toEnglishNumber(value) {

                return value
                    .replace(/[۰-۹]/g, function (digit) {
                        return digit.charCodeAt(0) - 1776;
                    })
                    .replace(/[٠-٩]/g, function (digit) {
                        return digit.charCodeAt(0) - 1632;
                    });
            }


            /*
            |--------------------------------------------------------------------------
            | فقط عدد
            |--------------------------------------------------------------------------
            */

            function sanitize(value) {

                return toEnglishNumber(value)
                    .replace(/\D/g, '');
            }


            /*
            |--------------------------------------------------------------------------
            | انتقال خودکار بین input ها
            |--------------------------------------------------------------------------
            */

            inputs.forEach(function (input, index) {

                input.addEventListener('input', function () {

                    const value = sanitize(this.value);

                    /*
                     * اگر کاربر چند رقم Paste کرد
                     */
                    if (value.length > 1) {

                        value
                            .slice(0, inputs.length)
                            .split('')
                            .forEach(function (digit, digitIndex) {

                                if (inputs[digitIndex]) {
                                    inputs[digitIndex].value = digit;
                                }

                            });

                        const nextIndex = Math.min(
                            value.length,
                            inputs.length - 1
                        );

                        inputs[nextIndex].focus();

                    } else {

                        this.value = value;

                        if (
                            value !== '' &&
                            index < inputs.length - 1
                        ) {
                            inputs[index + 1].focus();
                        }

                    }

                    checkComplete();

                });


                /*
                |--------------------------------------------------------------------------
                | Backspace
                |--------------------------------------------------------------------------
                */

                input.addEventListener('keydown', function (event) {

                    if (event.key === 'Backspace') {

                        if (
                            this.value === '' &&
                            index > 0
                        ) {
                            inputs[index - 1].focus();
                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | فلش چپ
                    |--------------------------------------------------------------------------
                    */

                    if (
                        event.key === 'ArrowLeft' &&
                        index > 0
                    ) {
                        inputs[index - 1].focus();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | فلش راست
                    |--------------------------------------------------------------------------
                    */

                    if (
                        event.key === 'ArrowRight' &&
                        index < inputs.length - 1
                    ) {
                        inputs[index + 1].focus();
                    }

                });

            });


            /*
            |--------------------------------------------------------------------------
            | Paste
            |--------------------------------------------------------------------------
            */

            inputs.forEach(function (input) {

                input.addEventListener('paste', function (event) {

                    event.preventDefault();

                    const pastedText = (
                        event.clipboardData ||
                        window.clipboardData
                    ).getData('text');

                    const value = sanitize(pastedText);

                    if (!value) {
                        return;
                    }

                    value
                        .slice(0, inputs.length)
                        .split('')
                        .forEach(function (digit, index) {

                            if (inputs[index]) {
                                inputs[index].value = digit;
                            }

                        });

                    const nextIndex = Math.min(
                        value.length,
                        inputs.length - 1
                    );

                    inputs[nextIndex].focus();

                    checkComplete();

                });

            });


            /*
            |--------------------------------------------------------------------------
            | بررسی کامل بودن OTP
            |--------------------------------------------------------------------------
            */

            function checkComplete() {

                const complete = inputs.every(function (input) {
                    return input.value.length === 1;
                });

                if (complete) {

                    /*
                     * جلوگیری از ارسال چندباره
                     */
                    submitBtn.disabled = true;

                    form.requestSubmit();
                }

            }


            /*
            |--------------------------------------------------------------------------
            | جلوگیری از چند بار Submit
            |--------------------------------------------------------------------------
            */

            form.addEventListener('submit', function () {

                submitBtn.disabled = true;

                submitBtn.innerHTML =
                    '<i class="fad fa-spinner fa-spin ml-1"></i> در حال بررسی...';

            });


            /*
            |--------------------------------------------------------------------------
            | Timer
            |--------------------------------------------------------------------------
            */

            let timeLeft = 120;

            let timer = null;


            function startTimer() {

                if (timer) {
                    clearInterval(timer);
                }

                resendBtn.style.display = 'none';

                countdownContainer.style.display = 'flex';

                timeLeft = 120;

                updateTimer();


                timer = setInterval(function () {

                    timeLeft--;

                    updateTimer();


                    if (timeLeft <= 0) {

                        clearInterval(timer);

                        countdownContainer.style.display = 'none';

                        resendBtn.style.display = 'inline-flex';

                    }

                }, 1000);

            }


            function updateTimer() {

                const minutes = Math.floor(timeLeft / 60);

                const seconds = timeLeft % 60;

                const formattedMinutes =
                    minutes.toString().padStart(2, '0');

                const formattedSeconds =
                    seconds.toString().padStart(2, '0');

                timerDisplay.textContent =
                    formattedMinutes + ':' + formattedSeconds;

            }


            startTimer();


            /*
            |--------------------------------------------------------------------------
            | Resend OTP
            |--------------------------------------------------------------------------
            */

            resendBtn.addEventListener('click', async function () {

                if (resendBtn.disabled) {
                    return;
                }

                resendBtn.disabled = true;

                resendBtn.innerHTML =
                    '<i class="fad fa-spinner fa-spin ml-1"></i> در حال ارسال...';


                try {

                    const response = await fetch(
                        "{{ route('verify.resend') }}",
                        {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN':
                                    "{{ csrf_token() }}",

                                'Accept':
                                    'application/json',

                                'Content-Type':
                                    'application/json'
                            },

                            credentials: 'same-origin'
                        }
                    );


                    let data = {};

                    try {
                        data = await response.json();
                    } catch (error) {
                        data = {};
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HTTP error
                    |--------------------------------------------------------------------------
                    */

                    if (! response.ok) {

                        showMessage(
                            data.message ||
                            'ارسال کد با مشکل مواجه شد.',
                            'error'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Success
                    |--------------------------------------------------------------------------
                    */

                    if (data.status === 'success') {

                        inputs.forEach(function (input) {
                            input.value = '';
                        });

                        inputs[0].focus();

                        startTimer();

                    }


                    showMessage(
                        data.message ||
                        'عملیات انجام شد.',
                        data.status || 'error'
                    );


                } catch (error) {

                    showMessage(
                        'خطا در برقراری ارتباط با سرور.',
                        'error'
                    );

                } finally {

                    resendBtn.disabled = false;

                    resendBtn.innerHTML =
                        '<i class="fad fa-redo-alt ml-1"></i> دریافت مجدد کد تایید';

                }

            });


            /*
            |--------------------------------------------------------------------------
            | نمایش پیام
            |--------------------------------------------------------------------------
            */

            function showMessage(message, type) {

                const oldAlert =
                    document.querySelector('.ajax-alert');

                if (oldAlert) {
                    oldAlert.remove();
                }


                const div = document.createElement('div');

                div.className =
                    type === 'success'
                        ? 'babi-auth-success ajax-alert mb-3 text-center'
                        : 'babi-auth-alert ajax-alert mb-3 text-center';


                /*
                 * مهم:
                 * textContent به جای innerHTML
                 */
                div.textContent = message;


                const header =
                    document.querySelector('.babi-auth-header');

                if (header) {
                    header.after(div);
                }


                setTimeout(function () {

                    if (div) {
                        div.remove();
                    }

                }, 3000);

            }

        });
    </script>

@endpush
