@extends('admin.layouts.master')

@section('content')

    <main class="main-content">

        @include('admin.layouts.error')

        <div class="card">
            <div class="card-body">

                <div class="container">

                    <h5 class="card-title text-primary mb-4">
                        تکمیل اطلاعات احراز هویت
                    </h5>

                    <form action="{{ route('store.seller.verification') }}"
                          method="POST">

                        @csrf

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                نام
                            </label>

                            <div class="col-sm-10">
                                <input type="text"
                                       name="first_name"
                                       class="form-control"
                                       value="{{ old('first_name',$seller?->first_name) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                نام خانوادگی
                            </label>

                            <div class="col-sm-10">
                                <input type="text"
                                       name="last_name"
                                       class="form-control"
                                       value="{{ old('last_name',$seller?->last_name) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                نام برند (اختیاری)
                            </label>

                            <div class="col-sm-10">
                                <input type="text"
                                       name="brand_name"
                                       class="form-control"
                                       value="{{ old('brand_name',$seller?->brand_name) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                کد ملی
                            </label>

                            <div class="col-sm-10">
                                <input type="text"
                                       maxlength="10"
                                       name="national_code"
                                       class="form-control text-left"
                                       dir="ltr"
                                       value="{{ old('national_code',$seller?->national_code) }}">
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-info mb-4">
                            اطلاعات بانکی
                        </h6>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                شماره کارت
                            </label>

                            <div class="col-sm-10">
                                <input type="text"
                                       maxlength="16"
                                       name="card_number"
                                       class="form-control text-left"
                                       dir="ltr"
                                       value="{{ old('card_number',$seller?->card_number) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                شماره حساب
                            </label>

                            <div class="col-sm-10">
                                <input type="text"
                                       name="account_number"
                                       class="form-control text-left"
                                       dir="ltr"
                                       value="{{ old('account_number',$seller?->account_number) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                شماره شبا
                            </label>

                            <div class="col-sm-10">
                                <input type="text"
                                       name="iban"
                                       class="form-control text-left"
                                       dir="ltr"
                                       placeholder="IRxxxxxxxxxxxxxxxxxxxxxxxx"
                                       value="{{ old('iban',$seller?->iban) }}">
                            </div>
                        </div>

                        <hr>

                        <div class="alert alert-warning">

                            <strong>توجه:</strong>

                            پس از ثبت اطلاعات، درخواست شما جهت بررسی برای مدیریت ارسال خواهد شد.
                            تا زمان تایید اطلاعات بانکی امکان تسویه حساب وجود نخواهد داشت.

                        </div>

                        <div class="form-group row mt-4">

                            <button type="submit"
                                    class="btn btn-success btn-uppercase">

                                <i class="ti-check-box m-r-5"></i>

                                ذخیره اطلاعات

                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>

    </main>

@endsection
