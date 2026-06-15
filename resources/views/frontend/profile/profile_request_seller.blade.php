@extends('frontend.layouts.master')

@section('content')

    @include('frontend.layouts.header')

    <main class="main-content dt-sl mb-3">
        <div class="container main-container">

            <div class="row">

                {{-- Sidebar --}}
                @include('frontend.profile.sidebar')

                {{-- Content --}}
                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

                    @if(session()->has('message'))
                        <div class="alert alert-info mb-3">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="profile-form-card">

                        <div class="profile-form-header">
                            <h4>درخواست همکاری به عنوان طراح</h4>
                            <p>
                                اطلاعات خود را تکمیل کنید تا درخواست همکاری شما بررسی شود.
                            </p>
                        </div>

                        <form action="{{ route('profile.store.request.seller') }}"
                              method="POST"
                              enctype="multipart/form-data">

                            @csrf

                            {{-- وضعیت درخواست --}}
                            @if($sellerRequest)

                                @if($sellerRequest->status === \App\Enums\SellerRequestStatus::Pending->value)

                                    <div class="request-status-card status-warning mb-4">

                                        <div class="status-icon">
                                            <i class="mdi mdi-timer-sand"></i>
                                        </div>

                                        <div>
                                            <h6>در انتظار بررسی</h6>
                                            <p>درخواست شما توسط مدیریت در حال بررسی است.</p>
                                        </div>

                                    </div>

                                @elseif($sellerRequest->status === \App\Enums\SellerRequestStatus::Approved->value)

                                    <div class="request-status-card status-success mb-4">

                                        <div class="status-icon">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                        </div>

                                        <div>
                                            <h6>درخواست تایید شده</h6>
                                            <p>شما به عنوان طراح تایید شده‌اید.</p>
                                        </div>

                                    </div>

                                @elseif($sellerRequest->status === \App\Enums\SellerRequestStatus::Rejected->value)

                                    <div class="request-status-card status-danger mb-4">

                                        <div class="status-icon">
                                            <i class="mdi mdi-close-circle-outline"></i>
                                        </div>

                                        <div>
                                            <h6>درخواست رد شده</h6>
                                            <p>{{ $sellerRequest->admin_note }}</p>
                                        </div>

                                    </div>

                                @endif

                            @endif

                            <div class="row">

                                {{-- نام برند --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        نام برند یا نام هنری
                                    </label>

                                    <input type="text"
                                           class="form-control input-ui"
                                           name="brand_name"
                                           value="{{ old('brand_name',$sellerRequest?->brand_name) }}"
                                           placeholder="مثال: Graphic Master">

                                    @error('brand_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                                {{-- نمونه کار --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        لینک نمونه کار
                                    </label>

                                    <input type="url"
                                           class="form-control input-ui"
                                           name="portfolio"
                                           value="{{ old('portfolio',$sellerRequest?->portfolio) }}"
                                           placeholder="https://behance.net/...">

                                    @error('portfolio')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                                {{-- توضیحات --}}
                                <div class="col-12 mb-3">

                                    <label class="form-label">
                                        معرفی خود و سوابق طراحی
                                    </label>

                                    <textarea
                                        name="reason"
                                        rows="6"
                                        class="form-control input-ui"
                                        placeholder="خودتان را معرفی کنید و درباره تخصص‌ها و تجربیات خود توضیح دهید...">{{ old('reason',$sellerRequest?->reason) }}</textarea>

                                    @error('reason')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                                {{-- رزومه --}}
                                <div class="col-12 mb-3">

                                    <label class="form-label">
                                        رزومه (اختیاری)
                                    </label>

                                    <input type="file"
                                           name="resume"
                                           class="form-control input-ui">

                                    @error('resume')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>

                            {{-- پیام‌های مدیریتی --}}

                            @if(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Rejected->value &&
                                $sellerRequest->admin_note
                            )

                                <div class="alert alert-danger mt-3">

                                    درخواست شما در تاریخ

                                    {{ \Hekmatinasser\Verta\Verta::instance($sellerRequest->reviewed_at)->format('%d %B، %Y') }}

                                    بررسی شد و به دلیل

                                    <strong>{{ $sellerRequest->admin_note }}</strong>

                                    رد شده است.

                                </div>

                            @elseif(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Approved->value
                            )

                                <div class="alert alert-success mt-3">

                                    درخواست شما در تاریخ

                                    {{ \Hekmatinasser\Verta\Verta::instance($sellerRequest->reviewed_at)->format('%d %B، %Y') }}

                                    تایید شده است و اکنون می‌توانید محصولات و طرح‌های خود را ثبت نمایید.

                                </div>

                            @elseif(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Pending->value
                            )

                                <div class="alert alert-warning mt-3">
                                    درخواست شما در حال بررسی است.
                                </div>

                            @endif

                            {{-- احراز هویت --}}
                            @if($sellerRequest && $sellerRequest->status === \App\Enums\SellerRequestStatus::Approved->value)

                                <div class="verification-card mt-4">

                                    <div class="verification-content">

                                        <div class="verification-icon">
                                            <i class="mdi mdi-shield-check-outline"></i>
                                        </div>

                                        <div>
                                            <h5>احراز هویت فروشنده</h5>

                                            <p>
                                                برای فعال سازی کامل حساب فروشندگی،
                                                احراز هویت خود را تکمیل کنید.
                                            </p>
                                        </div>

                                    </div>

                                    <a href="{{ route('profile.verification.seller') }}"
                                       class="verification-btn">

                                        شروع احراز هویت

                                    </a>

                                </div>

                            @elseif(
                                $sellerRequest &&
                                $sellerRequest->status === \App\Enums\SellerRequestStatus::Pending->value
                            )

                                <div class="form-actions">

                                    <button type="button"
                                            class="btn-save btn-disabled"
                                            disabled>

                                        <i class="mdi mdi-clock-outline"></i>

                                        درخواست شما در حال بررسی است

                                    </button>

                                </div>

                            @else

                                <div class="form-actions">

                                    <button type="submit"
                                            class="btn-save">

                                        <i class="mdi mdi-account-check-outline"></i>

                                        ارسال درخواست همکاری

                                    </button>

                                </div>

                            @endif

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </main>

@endsection
