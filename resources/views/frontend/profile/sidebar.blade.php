@php
    $userImage = auth()->user()->image
        ? url('images/users/small/' . auth()->user()->image)
        : url('images/users/default-avatar.png');
@endphp

<div class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
    <div class="profile-sidebar">

        <!-- User Card -->
        <div class="profile-card">

            <div class="profile-cover"></div>

            <div class="profile-info text-center">
                <div class="profile-avatar">
                    <img src="{{ $userImage }}" alt="{{auth()->user()->name}}">
                </div>

                <h5 class="profile-name">
                    {{auth()->user()->name}}
                </h5>

                <p class="profile-mobile">
                    {{auth()->user()->mobile}}
                </p>

                <div class="profile-actions">
                    <a href="#" class="profile-btn">
                        <i class="mdi mdi-lock-reset"></i>
                        <span>تغییر رمز</span>
                    </a>

                    <a href="{{route('logout')}}" class="profile-btn danger">
                        <i class="mdi mdi-logout-variant"></i>
                        <span>خروج</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Menu -->
        <div class="profile-menu-card">

            <div class="menu-title">
                حساب کاربری شما
            </div>

            <ul class="profile-menu">

                <li>
                    <a href="{{route('profile')}}"
                       @if(Route::currentRouteName() == 'profile') class="active" @endif>
                        <i class="mdi mdi-account-circle-outline"></i>
                        <span>پروفایل</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('profile.orders')}}"
                       @if(Route::currentRouteName() == 'profile.orders') class="active" @endif>
                        <i class="mdi mdi-basket"></i>
                        <span>همه سفارش ها</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('profile.favorites')}}"
                       @if(Route::currentRouteName() == 'profile.favorites') class="active" @endif>
                        <i class="mdi mdi-heart-outline"></i>
                        <span>لیست علاقه‌مندی ها</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('profile.downloads')}}"
                       @if(Route::currentRouteName() == 'profile.downloads') class="active" @endif>
                        <i class="mdi mdi-download"></i>
                        <span>فایل های دانلود شده</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('profile.request.seller')}}"
                       @if(Route::currentRouteName() == 'profile.request.seller') class="active" @endif>
                        <i class="mdi mdi-account-tie"></i>
                        <span>درخواست همکاری</span>
                    </a>
                </li>

            </ul>

        </div>

    </div>
</div>
