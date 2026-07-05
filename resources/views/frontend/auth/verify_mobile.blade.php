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
                            <img src="{{ url('frontend/img/logo.png') }}" class="img-fluid babi-auth-logo" alt="logo">
                        </a>
                    </div>

                    <div class="babi-auth-wrapper">
                        <div class="babi-auth-header mb-4 text-center">
                            <h2 class="babi-auth-title">تایید شماره همراه</h2>
                            <p class="babi-auth-subtitle mt-2">کد ۵ رقمی ارسال شده را وارد نمایید</p>
                        </div>

                        @if(session()->has('message'))
                            <div class="babi-auth-alert mb-3 text-center">
                                <i class="fad fa-exclamation-triangle ml-2"></i> {{ session('message') }}
                            </div>
                        @endif

                        @if(session()->has('success_message'))
                            <div class="babi-auth-success mb-3 text-center">
                                <i class="fad fa-check-circle ml-2"></i> {{ session('success_message') }}
                            </div>
                        @endif

                        <div class="babi-mobile-info-box d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fad fa-mobile-android text-primary ml-2 font-size-20"></i>
                                <span class="babi-phone-num" dir="ltr">{{ session('mobile') ?? '---' }}</span>
                            </div>
                            <a href="{{ route('register') }}" class="babi-edit-phone-btn">
                                <i class="fal fa-edit ml-1"></i> ویرایش شماره
                            </a>
                        </div>

                        <form action="{{ route('verify.code') }}" method="POST" id="otp-form">
                            @csrf

                            <div class="form-row justify-content-center mb-4">
                                <div class="babi-otp-container" dir="ltr">
                                    <input
                                        name="code[]"
                                        type="tel"
                                        maxlength="1"
                                        class="babi-otp-input"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        autocomplete="off"
                                        autofocus
                                        required
                                    >
                                    <input
                                        name="code[]"
                                        type="tel"
                                        maxlength="1"
                                        class="babi-otp-input"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        autocomplete="off"
                                        required
                                    >
                                    <input
                                        name="code[]"
                                        type="tel"
                                        maxlength="1"
                                        class="babi-otp-input"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        autocomplete="off"
                                        required
                                    >
                                    <input
                                        name="code[]"
                                        type="tel"
                                        maxlength="1"
                                        class="babi-otp-input"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        autocomplete="off"
                                        required
                                    >
                                    <input
                                        name="code[]"
                                        type="tel"
                                        maxlength="1"
                                        class="babi-otp-input"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-row justify-content-center mb-4">
                                <div class="babi-timer-wrapper text-center">
                                    <div id="babi-countdown-container" class="d-flex align-items-center justify-content-center">
                                        <i class="fal fa-clock ml-2 text-muted"></i>
                                        <span class="text-muted ml-1">ارسال مجدد کد پس از:</span>

                                    </div>

                                    <button type="button" id="babi-resend-btn" class="btn babi-resend-link" style="display:none;">
                                        دریافت مجدد کد تایید
                                    </button>
                                </div>
                            </div>

                            <div class="form-row">
                                <button type="submit" class="babi-btn-primary mx-auto w-100">
                                    تایید و ادامه مسیر <i class="fad fa-arrow-left mr-2"></i>
                                </button>
                            </div>
                        </form>

                        <div class="babi-auth-footer mt-4 text-center">
                            <span class="text-muted">کاربر جدید هستید؟</span>
                            <a href="{{ route('register') }}" class="babi-redirect-link mr-1">ثبت نام در سایت</a>
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

            const inputs = document.querySelectorAll('.babi-otp-input');
            const form = document.getElementById('otp-form');

            const resendBtn = document.getElementById('babi-resend-btn');
            const countdownContainer = document.getElementById('babi-countdown-container');
            const timerDisplay = document.getElementById('countdown-verify-end');

            // ================= OTP =================

            function toEnglishNumber(str) {
                return str
                    .replace(/[۰-۹]/g, d => d.charCodeAt(0) - 1776)
                    .replace(/[٠-٩]/g, d => d.charCodeAt(0) - 1632);
            }

            inputs.forEach((input, index) => {

                input.addEventListener('input', function () {

                    this.value = toEnglishNumber(this.value).replace(/\D/g, '');

                    if (this.value.length > 1) {
                        this.value = this.value.charAt(0);
                    }

                    if (this.value !== '' && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                        inputs[index + 1].select();
                    }

                    if ([...inputs].every(i => i.value.length === 1)) {
                        form.submit();
                    }

                });

                input.addEventListener('keydown', function (e) {

                    if (e.key === 'Backspace') {

                        if (this.value === '' && index > 0) {
                            inputs[index - 1].focus();
                        }

                    }

                });

            });

            // ================= Timer =================

            let timeLeft = 120;

            function startTimer() {

                resendBtn.style.display = 'none';
                countdownContainer.style.display = 'flex';

                const timer = setInterval(function () {

                    let minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;

                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    seconds = seconds < 10 ? '0' + seconds : seconds;

                    timerDisplay.innerText = minutes + ':' + seconds;

                    timeLeft--;

                    if (timeLeft < 0) {

                        clearInterval(timer);

                        countdownContainer.style.display = 'none';
                        resendBtn.style.display = 'inline-flex';

                    }

                }, 1000);

            }

            startTimer();

            // ================= Resend OTP =================

            resendBtn.addEventListener('click', function () {

                resendBtn.disabled = true;
                resendBtn.innerHTML = '<i class="fad fa-spinner fa-spin ml-1"></i> در حال ارسال...';

                fetch("{{ route('verify.resend') }}", {

                    method: "POST",

                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    }

                })

                    .then(response => response.json())

                    .then(data => {

                        showMessage(data.message, data.status);

                        if (data.status === 'success') {

                            inputs.forEach(input => input.value = '');

                            inputs[0].focus();

                            timeLeft = 120;

                            startTimer();

                        }

                    })

                    .catch(() => {

                        showMessage('خطا در برقراری ارتباط با سرور', 'error');

                    })

                    .finally(() => {

                        resendBtn.disabled = false;
                        resendBtn.innerHTML = '<i class="fad fa-redo-alt ml-1"></i> دریافت مجدد کد تایید';

                    });

            });

            // ================= Alert =================

            function showMessage(message, type) {

                const old = document.querySelector('.ajax-alert');

                if (old) {
                    old.remove();
                }

                const div = document.createElement('div');

                div.className =
                    (type === 'success')
                        ? 'babi-auth-success ajax-alert mb-3 text-center'
                        : 'babi-auth-alert ajax-alert mb-3 text-center';

                div.innerHTML = message;

                document.querySelector('.babi-auth-header').after(div);

                setTimeout(function () {

                    div.remove();

                }, 3000);

            }

        });
    </script>
@endpush
