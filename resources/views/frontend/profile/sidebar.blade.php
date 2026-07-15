@php
    $userImage = auth()->user()->image
        ? url('images/users/small/' . auth()->user()->image)
        : url('images/users/default-avatar.png');
@endphp

<div class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
    <div class="profile-sidebar">

        <div class="profile-card babi-sidebar-card">
            <div class="profile-cover"></div>
            <div class="profile-info text-center">
                <div class="profile-avatar">
                    <img src="{{ $userImage }}" alt="{{auth()->user()->name}}">
                </div>
                <h5 class="profile-name js-profile-name">{{ auth()->user()->name }}</h5>
                <p class="profile-mobile">{{auth()->user()->mobile}}</p>

                <div class="profile-actions">
                    <button type="button" class="profile-btn" id="babi-toggle-password-btn">
                        <i class="mdi mdi-lock-reset"></i>
                        <span>تغییر رمز</span>
                    </button>
                    <a href="{{route('logout')}}" class="profile-btn danger">
                        <i class="mdi mdi-logout-variant"></i>
                        <span>خروج</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="babi-password-isolated-card" id="babi-password-drawer">
            <div class="menu-title d-flex align-items-center justify-content-between mb-3">
                <span>تغییر رمز عبور</span>
                <i class="mdi mdi-shield-lock-outline text-muted" style="font-size: 18px;"></i>
            </div>

            <form method="POST" action="{{ route('password.update') }}" id="babi-sidebar-pass-form">
                @csrf
                @method('PUT')

                <div class="babi-mini-row">
                    <input type="password" class="input-ui text-left" dir="ltr" name="current_password" placeholder="رمز عبور فعلی" required>
                    <span class="babi-mini-line"></span>
                </div>

                <div class="babi-mini-row">
                    <input type="password" class="input-ui text-left" dir="ltr" name="password" id="side-new-pass" placeholder="رمز عبور جدید" required>
                    <span class="babi-mini-line"></span>
                    <span class="babi-sidebar-err">حداقل باید ۸ کاراکتر باشد</span>
                </div>

                <div class="babi-mini-row">
                    <input type="password" class="input-ui text-left" dir="ltr" name="password_confirmation" id="side-confirm-pass" placeholder="تکرار رمز عبور جدید" required>
                    <span class="babi-mini-line"></span>
                    <span class="babi-sidebar-err">با رمز عبور جدید مطابقت ندارد</span>
                </div>

                <button type="submit" class="btn-primary-cm btn-with-icon w-100 babi-btn-glow-mini mt-2">
                    <i class="mdi mdi-check-all"></i> بروزرسانی رمز عبور
                </button>
            </form>
        </div>

        <div class="profile-menu-card babi-sidebar-card mt-3">
            <div class="menu-title">حساب کاربری شما</div>
            <ul class="profile-menu">
                <li>
                    <a href="{{route('profile')}}" @if(Route::currentRouteName() == 'profile') class="active" @endif>
                        <i class="mdi mdi-account-circle-outline"></i>
                        <span>پروفایل</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('profile.orders')}}" @if(Route::currentRouteName() == 'profile.orders') class="active" @endif>
                        <i class="mdi mdi-basket"></i>
                        <span>همه سفارش ها</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('profile.favorites')}}" @if(Route::currentRouteName() == 'profile.favorites') class="active" @endif>
                        <i class="mdi mdi-heart-outline"></i>
                        <span>لیست علاقه‌مندی ها</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('profile.downloads')}}" @if(Route::currentRouteName() == 'profile.downloads') class="active" @endif>
                        <i class="mdi mdi-download"></i>
                        <span>فایل های دانلود شده</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('profile.request.seller')}}" @if(Route::currentRouteName() == 'profile.request.seller') class="active" @endif>
                        <i class="mdi mdi-account-tie"></i>
                        <span>درخواست همکاری</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('babi-toggle-password-btn');
        const drawer = document.getElementById('babi-password-drawer');
        const newPass = document.getElementById('side-new-pass');
        const confirmPass = document.getElementById('side-confirm-pass');
        const passForm = document.getElementById('babi-sidebar-pass-form');

        // باز و بسته شدن کارت مستقل با افکت آکاردئونی روان بدون تخریب کارت بالا
        toggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            drawer.classList.toggle('is-open');
            toggleBtn.classList.toggle('active-btn');
        });

        newPass.addEventListener('input', function() {
            const row = this.parentElement;
            if(this.value.length === 0) { row.classList.remove('has-err','is-ok'); return; }
            if(this.value.length < 8) { row.classList.add('has-err'); row.classList.remove('is-ok'); }
            else { row.classList.remove('has-err'); row.classList.add('is-ok'); }
            if(confirmPass.value.length > 0) confirmPass.dispatchEvent(new Event('input'));
        });

        confirmPass.addEventListener('input', function() {
            const row = this.parentElement;
            if(this.value.length === 0) { row.classList.remove('has-err','is-ok'); return; }
            if(this.value !== newPass.value) { row.classList.add('has-err'); row.classList.remove('is-ok'); }
            else { row.classList.remove('has-err'); row.classList.add('is-ok'); }
        });

        passForm.addEventListener('submit', function(e) {
            newPass.dispatchEvent(new Event('input'));
            confirmPass.dispatchEvent(new Event('input'));

            if(passForm.querySelectorAll('.babi-mini-row.has-err').length > 0) {
                e.preventDefault();
                drawer.classList.add('babi-shake');
                setTimeout(() => drawer.classList.remove('babi-shake'), 400);
            }
        });
    });
</script>
