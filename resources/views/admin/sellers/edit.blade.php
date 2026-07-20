@extends('admin.layouts.master')

@section('content')
    <main class="main-content">

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="container">

                    <h5 class="card-title text-primary mb-4">
                        <i class="ti-pencil-alt mr-2"></i> ویرایش اطلاعات فروشنده
                    </h5>

                    <form action="{{ route('admin.update.seller', $seller->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h6 class="text-secondary mb-3 font-weight-bold">اطلاعات فردی</h6>

                        {{-- first_name --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="first_name"
                                       class="form-control"
                                       value="{{ old('first_name', $seller->first_name) }}" required>
                                @error('first_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- last_name --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام خانوادگی <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="last_name"
                                       class="form-control"
                                       value="{{ old('last_name', $seller->last_name) }}" required>
                                @error('last_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- brand_name --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام برند</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="brand_name"
                                       class="form-control"
                                       value="{{ old('brand_name', $seller->brand_name) }}">
                                @error('brand_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- national_code --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">کد ملی <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="national_code"
                                       maxlength="10"
                                       class="form-control text-left"
                                       dir="ltr"
                                       value="{{ old('national_code', $seller->national_code) }}" required>
                                @error('national_code')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-info mb-3 font-weight-bold"><i class="ti-credit-card mr-1"></i> اطلاعات حساب بانکی</h6>

                        {{-- card_number --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">شماره کارت <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="card_number"
                                       maxlength="16"
                                       class="form-control text-left"
                                       dir="ltr"
                                       value="{{ old('card_number', $seller->card_number) }}" required>
                                @error('card_number')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- account_number --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">شماره حساب <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="account_number"
                                       class="form-control text-left"
                                       dir="ltr"
                                       value="{{ old('account_number', $seller->account_number) }}" required>
                                @error('account_number')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- iban --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">شماره شبا <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group" dir="ltr">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted">IR</span>
                                    </div>
                                    <input type="text"
                                           name="iban"
                                           class="form-control text-left"
                                           placeholder="120120000000001234567890"
                                           value="{{ old('iban', str_replace('IR', '', $seller->iban)) }}" required>
                                </div>
                                @error('iban')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="form-group row mt-4">
                            <div class="col-sm-10 offset-sm-2">
                                <a href="{{ route('seller.list') }}" class="btn btn-secondary mr-2">انصراف</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti-save mr-1"></i> ذخیره تغییرات
                                </button>
                            </div>
                        </div>

                    </form>

                </div>

            </div>
        </div>

    </main>
@endsection
