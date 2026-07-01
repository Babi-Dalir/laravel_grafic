@extends('frontend.auth.layouts.master')
@section('content')
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7 col-12 mx-auto">
                    <div class="logo-area text-center mb-3">
                        <a href="#"><img src="{{url('frontend/img/logo.png')}}" class="img-fluid" alt="logo"></a>
                    </div>

                    <div class="auth-wrapper form-ui border pt-4 babi-fx-card">
                        <div class="section-title title-wide mb-1 no-after-title-wide">
                            <h2 class="font-weight-bold">ثبت نام</h2>
                        </div>

                        <form method="POST" action="{{ route('register') }}" id="babi-register-form">
                            @csrf
                            @include('frontend.auth.layouts.error')

                            <div class="form-row-title">
                                <h3>نام و نام خانوادگی</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-name">
                                <input type="text" class="input-ui pr-2 text-right font-numeric" name="name" id="input-name" value="{{ old('name') }}"
                                       placeholder="حداقل باید ۳ کاراکتر باشد" required autofocus>
                                <i class="mdi mdi-account-circle-outline"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">نام باید حداقل ۳ کاراکتر باشد</span>
                            </div>

                            <div class="form-row-title">
                                <h3>شماره موبایل</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-mobile">
                                <input type="text" class="input-ui pr-2 text-left font-numeric" dir="ltr" name="mobile" id="input-mobile" value="{{ old('mobile') }}"
                                       placeholder="مثال: 09123456789" required>
                                <i class="mdi mdi-cellphone-android"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">شماره موبایل باید ۱۱ رقم و با ۰۹ شروع شود</span>
                            </div>

                            <div class="form-row-title">
                                <h3>رمز عبور</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-password">
                                <input type="password" class="input-ui pr-2 text-left" dir="ltr" name="password" id="input-password"
                                       placeholder="حداقل ۸ کاراکتر" required>
                                <i class="mdi mdi-lock-open-variant-outline"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">رمز عبور باید حداقل ۸ کاراکتر باشد</span>
                            </div>

                            <div class="form-row-title">
                                <h3>تکرار رمز عبور</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-confirm">
                                <input type="password" class="input-ui pr-2 text-left" dir="ltr" name="password_confirmation" id="input-confirm"
                                       placeholder="تکرار رمز عبور را وارد کنید" required>
                                <i class="mdi mdi-lock-check-outline"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">تکرار رمز عبور با رمز عبور مطابقت ندارد</span>
                            </div>

                            <div class="form-row mt-3">
                                <button type="submit" class="btn-primary-cm btn-with-icon mx-auto w-100 babi-btn-glow" id="submit-btn">
                                    <i class="mdi mdi-account-circle-outline"></i>
                                    ثبت نام در سایت
                                </button>
                            </div>
                        </form>

                        <div class="form-footer mt-4 border-top pt-3">
                            <div class="text-center">
                                <span class="font-weight-bold text-muted">قبلا ثبت نام کرده اید؟</span>
                                <a href="{{route('login')}}" class="mr-2 text-link font-weight-bold">وارد شوید</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- ⚡ اسکریپت اعتبارسنجی در لحظه و انیمیشن‌های تعاملی فرم --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- بخش اول: افکت حرکت ۳ بعدی کارت با ماوس ---
            const card = document.querySelector('.babi-fx-card');
            if (card) {
                document.addEventListener('mousemove', function (e) {
                    const xAxis = (window.innerWidth / 2 - e.pageX) / 75;
                    const yAxis = (window.innerHeight / 2 - e.pageY) / 75;
                    card.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
                });
                document.addEventListener('mouseleave', function () {
                    card.style.transform = 'rotateY(0deg) rotateX(0deg)';
                });
            }

            // --- بخش دوم: منطق اعتبارسنجی در لحظه (Real-time Validation) ---
            const nameInp = document.getElementById('input-name');
            const mobileInp = document.getElementById('input-mobile');
            const passInp = document.getElementById('input-password');
            const confirmInp = document.getElementById('input-confirm');
            const registerForm = document.getElementById('babi-register-form');

            // توابع کمکی برای اعمال کلاس خطا و موفقیت
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

            // ۱. اعتبارسنجی نام (حداقل ۳ کاراکتر)
            nameInp.addEventListener('input', function() {
                const val = this.value.trim();
                const row = document.getElementById('row-name');
                if(val.length === 0) { clearStatus(row); return; }

                if(val.length < 3) { setInvalid(row); } else { setValid(row); }
            });

            // ۲. اعتبارسنجی موبایل (شروع با ۰۹ و دقیقاً ۱۱ رقم)
            mobileInp.addEventListener('input', function() {
                let val = this.value.trim();
                const row = document.getElementById('row-mobile');
                if(val.length === 0) { clearStatus(row); return; }

                // فرمت استاندارد موبایل ایران
                const mobileRegex = /^09[0-9]{9}$/;

                if (val.length !== 11 || !mobileRegex.test(val)) {
                    setInvalid(row);
                } else {
                    setValid(row);
                }
            });

            // ۳. اعتبارسنجی پسورد (حداقل ۸ کاراکتر)
            passInp.addEventListener('input', function() {
                const val = this.value;
                const row = document.getElementById('row-password');
                if(val.length === 0) { clearStatus(row); return; }

                if(val.length < 8) { setInvalid(row); } else { setValid(row); }

                // بررسی مجدد فیلد تکرار رمز عبور در صورت تغییر رمز اصلی
                if(confirmInp.value.length > 0) {
                    confirmInp.dispatchEvent(new Event('input'));
                }
            });

            // ۴. اعتبارسنجی تکرار پسورد (تطابق کامل با پسورد اصلی)
            confirmInp.addEventListener('input', function() {
                const val = this.value;
                const row = document.getElementById('row-confirm');
                if(val.length === 0) { clearStatus(row); return; }

                if(val !== passInp.value) { setInvalid(row); } else { setValid(row); }
            });

            // افکت فوکوس افکت متحرک خط‌ها
            const allInputs = document.querySelectorAll('.babi-animated-row input');
            allInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('is-focused');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('is-focused');
                });
            });

            // جلوگیری از ثبت فرم در صورت وجود خطای منطقی و لرزش فیلد خطادار
            registerForm.addEventListener('submit', function(e) {
                let hasError = false;

                // اجرای دستی رویدادها برای بررسی نهایی فیلدها قبل سابمیت
                nameInp.dispatchEvent(new Event('input'));
                mobileInp.dispatchEvent(new Event('input'));
                passInp.dispatchEvent(new Event('input'));
                confirmInp.dispatchEvent(new Event('input'));

                const errorRows = document.querySelectorAll('.babi-animated-row.has-error');
                if(errorRows.length > 0) {
                    e.preventDefault(); // مانع فرستادن فرم به سرور می‌شود

                    // افکت لرزش روی سطرهایی که خطا دارند
                    errorRows.forEach(row => {
                        row.classList.add('babi-shake');
                        setTimeout(() => row.classList.remove('babi-shake'), 500);
                    });
                }
            });

        });
    </script>
@endsection
