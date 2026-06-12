@extends('frontend.layouts.master')

@section('content')
    @include('frontend.layouts.header')
    <main class="main-content dt-sl mb-3">

        <div class="container main-container">

            <div class="row">

                @include('frontend.profile.sidebar')

                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

                    {{-- Header Card --}}
                    <div class="verification-header mb-4 p-4 rounded-lg shadow-sm">

                        <div class="d-flex align-items-center">

                            <div class="verification-icon mr-3">
                                <i class="mdi mdi-account-check-outline"></i>
                            </div>

                            <div>
                                <h4 class="mb-1">احراز هویت فروشنده</h4>
                                <small class="text-muted">
                                    برای فعال‌سازی فروشندگی، اطلاعات خود را تکمیل کنید
                                </small>
                            </div>

                        </div>

                    </div>

                    {{-- Form Card --}}
                    <div class="verification-card p-4 shadow-sm rounded-lg">

                        <form action="{{ route('store.seller.verification') }}" method="POST">
                            @csrf

                            {{-- Personal Info --}}
                            <h5 class="section-title mb-3">اطلاعات فردی</h5>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>نام</label>
                                    <input type="text" name="first_name"
                                           class="form-control"
                                           value="{{ old('first_name', $seller?->first_name) }}">

                                    @error('first_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>نام خانوادگی</label>
                                    <input type="text" name="last_name"
                                           class="form-control"
                                           value="{{ old('last_name', $seller?->last_name) }}">

                                    @error('last_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>نام برند (اختیاری)</label>
                                    <input type="text" name="brand_name"
                                           class="form-control"
                                           value="{{ old('brand_name', $seller?->brand_name) }}">

                                    @error('brand_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>کد ملی</label>
                                    <input type="text" name="national_code"
                                           maxlength="10"
                                           class="form-control text-left"
                                           dir="ltr"
                                           value="{{ old('national_code', $seller?->national_code) }}">

                                    @error('national_code')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>

                            <hr class="my-4">

                            {{-- Bank Info --}}
                            <h5 class="section-title mb-3 text-info">اطلاعات بانکی</h5>

                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <label>شماره کارت</label>
                                    <input type="text" name="card_number"
                                           maxlength="16"
                                           class="form-control text-left"
                                           dir="ltr"
                                           value="{{ old('card_number', $seller?->card_number) }}">

                                    @error('card_number')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>شماره حساب</label>
                                    <input type="text" name="account_number"
                                           class="form-control text-left"
                                           dir="ltr"
                                           value="{{ old('account_number', $seller?->account_number) }}">

                                    @error('account_number')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>شماره شبا</label>
                                    <input type="text" name="iban"
                                           class="form-control text-left"
                                           dir="ltr"
                                           placeholder="IRxxxxxxxxxxxxxxxx"
                                           value="{{ old('iban', $seller?->iban) }}">

                                    @error('iban')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>

                            {{-- Warning Box --}}
                            @if($seller?->status === \App\Enums\SellerStatus::Active->value)
                                <div class="alert alert-success mt-4">
                                    احراز هویت شما تکمیل است و مورد تایید مدیر قرار گرفته است.
                                </div>
                            @else
                                <div class="alert alert-warning mt-4">
                                    <strong>توجه:</strong>
                                    پس از ثبت اطلاعات، درخواست شما بررسی خواهد شد و امکان ویرایش محدود می‌شود.
                                </div>
                            @endif

                            {{-- Submit --}}
                            <div class="text-left mt-4">

                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="mdi mdi-content-save-outline ml-1"></i>
                                    ثبت و ارسال اطلاعات
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </main>
@endsection
