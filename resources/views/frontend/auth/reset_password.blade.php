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
                            <h2 class="font-weight-bold">رمز عبور جدید</h2>
                        </div>

                        <form method="POST" action="{{ route('password.update.otp') }}" id="babi-reset-form">
                            @csrf

                            @include('frontend.auth.layouts.error')

                            <div class="form-row-title">
                                <h3>رمز عبور جدید</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-password">
                                <input type="password" class="input-ui pr-2 text-left" dir="ltr" name="password" id="input-password"
                                       placeholder="حداقل ۸ کاراکتر" required autofocus>
                                <i class="mdi mdi-lock-open-variant-outline"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">رمز عبور باید حداقل ۸ کاراکتر باشد</span>
                            </div>

                            <div class="form-row-title">
                                <h3>تکرار رمز عبور جدید</h3>
                            </div>
                            <div class="form-row with-icon babi-animated-row" id="row-confirm">
                                <input type="password" class="input-ui pr-2 text-left" dir="ltr" name="password_confirmation" id="input-confirm"
                                       placeholder="تکرار رمز عبور را وارد کنید" required>
                                <i class="mdi mdi-lock-check-outline"></i>
                                <span class="babi-line-effect"></span>
                                <span class="babi-error-text">تکرار رمز عبور مطابقت ندارد</span>
                            </div>

                            <div class="form-row mt-3">
                                <button type="submit" class="btn-primary-cm btn-with-icon mx-auto w-100 babi-btn-glow">
                                    <i class="mdi mdi-check"></i>
                                    ذخیره رمز عبور جدید
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passInp = document.getElementById('input-password');
            const confirmInp = document.getElementById('input-confirm');
            const resetForm = document.getElementById('babi-reset-form');

            function setInvalid(rowElement) { rowElement.classList.add('has-error'); rowElement.classList.remove('is-valid'); }
            function setValid(rowElement) { rowElement.classList.remove('has-error'); rowElement.classList.add('is-valid'); }
            function clearStatus(rowElement) { rowElement.classList.remove('has-error', 'is-valid'); }

            passInp.addEventListener('input', function() {
                const val = this.value;
                const row = document.getElementById('row-password');
                if(val.length === 0) { clearStatus(row); return; }
                if(val.length < 8) { setInvalid(row); } else { setValid(row); }
                if(confirmInp.value.length > 0) { confirmInp.dispatchEvent(new Event('input')); }
            });

            confirmInp.addEventListener('input', function() {
                const val = this.value;
                const row = document.getElementById('row-confirm');
                if(val.length === 0) { clearStatus(row); return; }
                if(val !== passInp.value) { setInvalid(row); } else { setValid(row); }
            });

            resetForm.addEventListener('submit', function(e) {
                passInp.dispatchEvent(new Event('input'));
                confirmInp.dispatchEvent(new Event('input'));
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
