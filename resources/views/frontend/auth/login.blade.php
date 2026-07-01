@extends('frontend.auth.layouts.master')
@section('content')
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7 col-12 mx-auto">
                    <div class="logo-area text-center mb-3">
                        <a href="#"><img src="{{url('frontend/img/logo.png')}}" class="img-fluid" alt="logo"></a>
                    </div>

                    {{-- باکس لاگین هماهنگ با ثبت‌نام --}}
                    <div class="auth-wrapper form-ui border pt-4 babi-flat-card">
                        <div class="section-title title-wide mb-1 no-after-title-wide">
                            <h2 class="font-weight-bold text-gradient">ورود به حساب کاربری</h2>
                        </div>

                        <form method="POST" action="{{ route('login') }}" id="babi-login-form">
                            @csrf
                            @include('frontend.auth.layouts.error')

                            <div class="form-row-title">
                                <h3>شماره موبایل</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-mobile">
                                <input type="text" class="input-ui pr-2 text-left font-numeric" dir="ltr" name="mobile" id="input-mobile" value="{{ old('mobile') }}"
                                       placeholder="شماره موبایل خود را وارد نمایید" required autofocus>
                                <i class="mdi mdi-cellphone-android babi-icon-state"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">شماره موبایل معتبر ۱۱ رقمی (شروع با ۰۹) وارد کنید</span>
                            </div>

                            <div class="form-row-title">
                                <h3>رمز عبور</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-password">
                                <input type="password" class="input-ui pr-2 text-left" dir="ltr" name="password" id="input-password"
                                       placeholder="رمز عبور خود را وارد نمایید" required>
                                <i class="mdi mdi-lock-open-variant-outline babi-icon-state"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">رمز عبور خود را وارد کنید</span>
                            </div>

                            <div class="form-row mt-2">
                                <div class="custom-control custom-checkbox float-right mt-2">
                                    <input type="checkbox" class="custom-control-input" name="remember" id="customCheck3" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label text-muted" for="customCheck3">
                                        مرا به خاطر بسپار
                                    </label>
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <button type="submit" class="btn-primary-cm btn-with-icon mx-auto w-100 babi-btn-glow" id="submit-btn">
                                    <i class="mdi mdi-login-variant"></i>
                                    ورود به سایت
                                </button>
                            </div>
                        </form>

                        <div class="form-footer mt-4 border-top pt-3">
                            <div class="text-center">
                                <span class="font-weight-bold text-muted">کاربر جدید هستید؟</span>
                                <a href="{{route('register')}}" class="mr-2 text-link font-weight-bold">ثبت نام در سایت</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- ⚡ اسکریپت سبک و بهینه اعتبارسنجی و انیمیشن‌های لاگین --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileInp = document.getElementById('input-mobile');
            const passInp = document.getElementById('input-password');
            const loginForm = document.getElementById('babi-login-form');

            function setInvalid(rowElement) {
                rowElement.classList.add('has-error');
                rowElement.classList.remove('is-valid');
            }

            function setValid(rowElement) {
                rowElement.classList.remove('has-error');
                rowElement.classList.add('is-valid');
            }

            function clearStatus(rowElement) {
                rowElement.classList.remove('has-error', 'is-valid');
            }

            // ۱. بررسی آنی شماره موبایل هنگام تایپ
            mobileInp.addEventListener('input', function() {
                const val = this.value.trim();
                const row = document.getElementById('row-mobile');
                if(val.length === 0) { clearStatus(row); return; }

                const mobileRegex = /^09[0-9]{9}$/;
                if (val.length !== 11 || !mobileRegex.test(val)) {
                    setInvalid(row);
                } else {
                    setValid(row);
                }
            });

            // ۲. بررسی پسورد
            passInp.addEventListener('input', function() {
                const row = document.getElementById('row-password');
                if(this.value.length > 0) {
                    setValid(row);
                } else {
                    clearStatus(row);
                }
            });

            // مدیریت افکت فوکوس خط متحرک
            const allInputs = document.querySelectorAll('.babi-animated-row input');
            allInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('is-focused');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('is-focused');
                });
            });

            // گارد امنیتی سابمیت فرم همراه با افکت لرزش
            loginForm.addEventListener('submit', function(e) {
                mobileInp.dispatchEvent(new Event('input'));

                // اگر فیلد پسورد خالی بود خطا بدهد
                if(passInp.value.length === 0) {
                    setInvalid(document.getElementById('row-password'));
                }

                const errorRows = document.querySelectorAll('.babi-animated-row.has-error');
                if(errorRows.length > 0) {
                    e.preventDefault();
                    errorRows.forEach(row => {
                        row.classList.add('babi-shake');
                        setTimeout(() => row.classList.remove('babi-shake'), 400);
                    });
                }
            });
        });
    </script>
@endsection
