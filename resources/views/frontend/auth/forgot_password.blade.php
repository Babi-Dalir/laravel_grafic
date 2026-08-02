@extends('frontend.auth.layouts.master')
@section('content')
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7 col-12 mx-auto">
                    <div class="logo-area text-center mb-3">
                        <a href="{{ route('home') }}"><img src="{{ url('frontend/img/logo.png') }}" class="img-fluid" alt="logo"></a>
                    </div>

                    <div class="auth-wrapper form-ui border pt-4 babi-fx-card">
                        <div class="section-title title-wide mb-1 no-after-title-wide">
                            <h2 class="font-weight-bold">بازیابی رمز عبور</h2>
                        </div>

                        <form method="POST" action="{{ route('password.send.otp') }}" id="babi-forgot-form">
                            @csrf
                            @include('frontend.auth.layouts.error')

                            <div class="form-row-title">
                                <h3>شماره موبایل</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-mobile">
                                <input type="text" class="input-ui pr-2 text-left font-numeric" dir="ltr" name="mobile" id="input-mobile"
                                       value="{{ old('mobile') }}" placeholder="مثال: 09123456789" required autofocus>
                                <i class="mdi mdi-cellphone-android"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">شماره موبایل باید ۱۱ رقم و با ۰۹ شروع شود</span>
                            </div>

                            <div class="form-row mt-3">
                                <button type="submit" class="btn-primary-cm btn-with-icon mx-auto w-100 babi-btn-glow" id="submit-btn">
                                    <i class="mdi mdi-send"></i>
                                    ارسال کد تایید
                                </button>
                            </div>
                        </form>

                        <div class="form-footer mt-4 border-top pt-3">
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="mr-2 text-link font-weight-bold">بازگشت به ورود</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileInp = document.getElementById('input-mobile');
            const forgotForm = document.getElementById('babi-forgot-form');

            function setInvalid(rowElement) { rowElement.classList.add('has-error'); rowElement.classList.remove('is-valid'); }
            function setValid(rowElement) { rowElement.classList.remove('has-error'); rowElement.classList.add('is-valid'); }
            function clearStatus(rowElement) { rowElement.classList.remove('has-error', 'is-valid'); }

            mobileInp.addEventListener('input', function() {
                let val = this.value.trim();
                const row = document.getElementById('row-mobile');
                if(val.length === 0) { clearStatus(row); return; }

                const mobileRegex = /^09[0-9]{9}$/;
                if (val.length !== 11 || !mobileRegex.test(val)) {
                    setInvalid(row);
                } else {
                    setValid(row);
                }
            });

            forgotForm.addEventListener('submit', function(e) {
                mobileInp.dispatchEvent(new Event('input'));
                const errorRows = document.querySelectorAll('.babi-animated-row.has-error');
                if(errorRows.length > 0) {
                    e.preventDefault();
                    errorRows.forEach(row => {
                        row.classList.add('babi-shake');
                        setTimeout(() => row.classList.remove('babi-shake'), 500);
                    });
                }
            });
        });
    </script>
@endsection
