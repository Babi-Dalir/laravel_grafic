@extends('frontend.layouts.master')

@section('content')

    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-3">
        <div class="container main-container">

            <div class="row">

                @include('frontend.profile.sidebar')

                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

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

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                {{-- نام --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نام</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="name"
                                           value="{{ old('name', $user->name) }}">
                                    @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- نام کاربری --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نام کاربری</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="user_name"
                                           value="{{ old('user_name', $user->user_name) }}">
                                    @error('user_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- تلگرام --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تلگرام</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="telegram"
                                           value="{{ old('telegram', $user->userProfile?->telegram) }}">
                                    @error('telegram')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- ایتا --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ایتا</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="eta"
                                           value="{{ old('eta', $user->userProfile?->eta) }}">
                                    @error('eta')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- اینستاگرام --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اینستاگرام</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="instagram"
                                           value="{{ old('instagram', $user->userProfile?->instagram) }}">
                                    @error('instagram')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- وب سایت --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label">وب سایت</label>
                                    <input type="text"
                                           class="form-control input-ui"
                                           name="website"
                                           value="{{ old('website', $user->userProfile?->website) }}">
                                    @error('website')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- بیو --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label">درباره من</label>
                                    <textarea class="form-control input-ui"
                                              name="bio"
                                              rows="5">{{ old('bio', $user->userProfile?->bio) }}</textarea>

                                    @error('bio')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- عکس --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">عکس پروفایل</label>
                                    <input type="file" name="image" class="form-control">
                                    @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save">
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
