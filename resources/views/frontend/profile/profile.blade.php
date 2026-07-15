@extends('frontend.layouts.master')

@section('content')

    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-3">
        <div class="container main-container">

            <div class="row">

                @include('frontend.profile.sidebar')

                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

                    <div id="ajax-alert" class="alert d-none mb-3"></div>

                    @if(session()->has('message'))
                        <div class="alert alert-info mb-3">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="profile-form-card">

                        <div class="profile-form-header">
                            <h4>ویرایش اطلاعات شخصی</h4>
                            <p>اطلاعات حساب کاربری خود را به‌روز کنید</p>
                        </div>

                        <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                {{-- نام --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نام</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="name"
                                           value="{{ old('name', $user->name) }}">
                                    <small class="text-danger" id="error-name"></small>
                                </div>

                                {{-- نام کاربری --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نام کاربری</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="user_name"
                                           value="{{ old('user_name', $user->user_name) }}">
                                    <small class="text-danger" id="error-user_name"></small>
                                </div>

                                {{-- تلگرام --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تلگرام</label>
                                    <input type="text"
                                           class="form-control input-ui text-left"
                                           dir="ltr"
                                           name="telegram"
                                           value="{{ old('telegram', $user->userProfile?->telegram) }}">
                                    <small class="text-danger" id="error-telegram"></small>
                                </div>

                                {{-- ایتا --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ایتا</label>
                                    <input type="text"
                                           class="form-control input-ui text-left"
                                           dir="ltr"
                                           name="eta"
                                           value="{{ old('eta', $user->userProfile?->eta) }}">
                                    <small class="text-danger" id="error-eta"></small>
                                </div>

                                {{-- اینستاگرام --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اینستاگرام</label>
                                    <input type="text"
                                           class="form-control input-ui text-left"
                                           dir="ltr"
                                           name="instagram"
                                           value="{{ old('instagram', $user->userProfile?->instagram) }}">
                                    <small class="text-danger" id="error-instagram"></small>
                                </div>

                                {{-- وب سایت --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label">وب سایت</label>
                                    <input type="text"
                                           class="form-control input-ui text-left"
                                           dir="ltr"
                                           name="website"
                                           value="{{ old('website', $user->userProfile?->website) }}">
                                    <small class="text-danger" id="error-website"></small>
                                </div>

                                {{-- بیو --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label">درباره من</label>
                                    <textarea class="form-control input-ui"
                                              name="bio"
                                              rows="5">{{ old('bio', $user->userProfile?->bio) }}</textarea>
                                    <small class="text-danger" id="error-bio"></small>
                                </div>

                                {{-- عکس --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">عکس پروفایل</label>
                                    <input type="file" name="image" id="profile-image-input" class="form-control">
                                    <small class="text-danger" id="error-image"></small>
                                </div>

                                {{-- باکس پیش‌نمایش تصویر (بسیار ساده و بدون تداخل در استایل) --}}
                                <div class="col-md-6 mb-3 d-flex align-items-center">
                                    <div class="mt-3">
                                        <img id="profile-image-preview"
                                             src="{{ $user->image ? asset('storage/users/' . $user->image) : asset('assets/images/default-avatar.png') }}"
                                             alt="پیش‌نمایش"
                                             class="rounded-circle"
                                             style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #ddd;">
                                    </div>
                                </div>

                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save" id="submit-btn">
                                    ثبت اطلاعات
                                </button>
                            </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </main>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const $form = $('#profile-update-form');
            const $submitBtn = $('#submit-btn');
            const $alert = $('#ajax-alert');

            // ۱. پیش‌نمایش آنی عکس
            $('#profile-image-input').on('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        $('#profile-image-preview').attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // ۲. اعتبارسنجی آنی در حین خروج از فیلدها (Blur) + پاک کردن ارور هنگام شروع تایپ (Input)
            $form.find('input, textarea').on('blur', function () {
                const $input = $(this);
                const fieldName = $input.attr('name');

                if (!fieldName || fieldName === 'image' || fieldName === '_token') return;

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
                // 🟢 به محض اینکه کاربر شروع به تایپ در هر فیلد کند، ارور مربوط به آن بلافاصله پاک می‌شود
                const fieldName = $(this).attr('name');
                $('#error-' + fieldName).text('');
            });

            // ۳. ثبت نهایی کل فرم با Ajax بدون رفرش
            $form.on('submit', function (e) {
                e.preventDefault();

                // ریست کردن پیام‌های خطا
                $('small.text-danger').text('');
                $alert.addClass('d-none').removeClass('alert-success alert-danger').text('');

                // غیرفعال کردن دکمه و نمایش حالت در حال لود
                $submitBtn.attr('disabled', true).text('در حال ثبت اطلاعات...');

                const formData = new FormData(this);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $submitBtn.removeAttr('disabled').text('ثبت اطلاعات');

                        // نمایش پیغام موفقیت
                        $alert.removeClass('d-none alert-danger').addClass('alert-success').text(response.message || "اطلاعات شما با موفقیت ثبت شد");
                        $('html, body').animate({ scrollTop: 0 }, 'slow');

                        // بروزرسانی نام کاربر در سایدبار بدون نیاز به رفرش
                        if (response.new_name) {
                            $('.js-profile-name').text(response.new_name);
                            console.log("Profile name updated to: " + response.new_name);
                        }

                        // بروزرسانی عکس کاربر در سایدبار و هدر بدون نیاز به رفرش
                        if (response.new_image_url) {
                            console.log("Updating avatar source across page to: " + response.new_image_url);
                            $('.profile-avatar img, .user-avatar img').attr('src', response.new_image_url);
                        }
                    },
                    error: function (xhr) {
                        $submitBtn.removeAttr('disabled').text('ثبت اطلاعات');
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
        });
    </script>
@endpush
