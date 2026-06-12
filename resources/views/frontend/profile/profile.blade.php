@extends('frontend.layouts.master')
@section('content')

    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-3">
        <div class="container main-container">

            <div class="row">

                @include('frontend.profile.sidebar')

                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

                    @if(session()->has('message'))
                        <div class="alert alert-info">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="px-3 px-res-0">

                        <div class="section-title mb-3">
                            <h2>ویرایش اطلاعات شخصی</h2>
                        </div>

                        <div class="form-ui additional-info dt-sl dt-sn pt-4">

                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">

                                    {{-- نام --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-row-title">
                                            <h3>نام</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="text" class="input-ui pr-2"
                                                   name="name"
                                                   value="{{ old('name', $user->name) }}">
                                        </div>

                                        @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- نام کاربری --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-row-title">
                                            <h3>نام کاربری</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="text" class="input-ui pr-2"
                                                   name="user_name"
                                                   value="{{ old('user_name', $user->user_name) }}">
                                        </div>

                                        @error('user_name')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- تلفن --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-row-title">
                                            <h3>تلفن تماس</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="text" class="input-ui pl-2 text-left dir-ltr"
                                                   name="phone"
                                                   value="{{ old('phone', $user->userProfile?->phone) }}">
                                        </div>

                                        @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- تلگرام --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-row-title">
                                            <h3>تلگرام</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="text" class="input-ui"
                                                   name="telegram"
                                                   value="{{ old('telegram', $user->userProfile?->telegram) }}">
                                        </div>

                                        @error('telegram')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- ایتا --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-row-title">
                                            <h3>ایتا</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="text" class="input-ui"
                                                   name="eta"
                                                   value="{{ old('eta', $user->userProfile?->eta) }}">
                                        </div>

                                        @error('eta')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- اینستاگرام --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-row-title">
                                            <h3>اینستاگرام</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="text" class="input-ui"
                                                   name="instagram"
                                                   value="{{ old('instagram', $user->userProfile?->instagram) }}">
                                        </div>

                                        @error('instagram')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- وب سایت --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-row-title">
                                            <h3>وب سایت</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="text" class="input-ui"
                                                   name="website"
                                                   value="{{ old('website', $user->userProfile?->website) }}">
                                        </div>

                                        @error('website')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- بیو --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-row-title">
                                            <h3>درباره من</h3>
                                        </div>

                                        <div class="form-row">
                                        <textarea class="input-ui"
                                                  name="bio"
                                                  rows="5">{{ old('bio', $user->userProfile?->bio) }}</textarea>
                                        </div>

                                        @error('bio')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- عکس --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-row-title">
                                            <h3>عکس پروفایل</h3>
                                        </div>

                                        <div class="form-row">
                                            <input type="file" name="image">
                                        </div>

                                        @error('image')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                </div>

                                <div class="text-left mt-3">
                                    <button type="submit" class="btn-primary-cm btn-with-icon">
                                        ثبت اطلاعات کاربری
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

@endsection
