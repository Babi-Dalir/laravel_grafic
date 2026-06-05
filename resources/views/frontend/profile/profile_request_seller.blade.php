@extends('frontend.layouts.master')
@section('content')
    @include('frontend.layouts.header')
    <main class="main-content dt-sl mb-3">
        <div class="container main-container">
            <div class="row">
                <!-- Start Sidebar -->
                @include('frontend.profile.sidebar')
                <!-- End Sidebar -->
                <!-- Start Content -->
                <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
                    <div class="row">
                        <div class="row">
                            @if(\Illuminate\Support\Facades\Session::has('message'))
                                <div class="alert alert-info">
                                    <div>{{session('message')}}</div>
                                </div>
                            @endif

                        </div>
                        <div class="col-12">
                            <div class="px-3 px-res-0">

                                <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                                    <h2>درخواست همکاری به عنوان طراح</h2>
                                </div>

                                <div class="form-ui additional-info dt-sl dt-sn pt-4">

                                    <form action="{{ route('profile.store.request.seller') }}"
                                          method="POST"
                                          enctype="multipart/form-data">

                                        @csrf

                                        <div class="row">

                                            {{-- نام برند --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>نام برند یا نام هنری</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text"
                                                           class="input-ui pr-2"
                                                           name="brand_name"
                                                           value="{{ old('brand_name') }}"
                                                           placeholder="مثال: Graphic Master">
                                                </div>
                                            </div>

                                            {{-- لینک نمونه کار --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>لینک نمونه کار</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="url"
                                                           class="input-ui pr-2"
                                                           name="portfolio"
                                                           value="{{ old('portfolio') }}"
                                                           placeholder="https://behance.net/...">
                                                </div>
                                            </div>

                                            {{-- وضعیت درخواست --}}
                                            @if($sellerRequest)
                                                <div class="col-md-12 mb-3">

                                                    <div class="form-row-title">
                                                        <h3>
                                                            وضعیت درخواست :

                                                            @if($sellerRequest->status === \App\Enums\SellerRequestStatus::Pending->value)

                                                                <span class="badge badge-warning">
                                            در انتظار بررسی
                                        </span>

                                                            @elseif($sellerRequest->status === \App\Enums\SellerRequestStatus::Approved->value)

                                                                <span class="badge badge-success">
                                            تایید شده
                                        </span>

                                                            @elseif($sellerRequest->status === \App\Enums\SellerRequestStatus::Rejected->value)

                                                                <span class="badge badge-danger">
                                            رد شده
                                        </span>

                                                            @endif

                                                        </h3>
                                                    </div>

                                                </div>
                                            @endif

                                            {{-- توضیحات --}}
                                            <div class="col-md-12 mb-3">

                                                <div class="form-row-title">
                                                    <h3>معرفی خود و سوابق طراحی</h3>
                                                </div>

                                                <div class="form-row">
                            <textarea
                                name="reason"
                                rows="6"
                                class="input-ui pr-2"
                                placeholder="خودتان را معرفی کنید و درباره سوابق طراحی، تخصص‌ها و تجربیاتتان توضیح دهید...">{{ old('reason') }}</textarea>
                                                </div>

                                            </div>

                                            {{-- فایل رزومه --}}
                                            <div class="col-md-12 mb-3">

                                                <div class="form-row-title">
                                                    <h3>رزومه (اختیاری)</h3>
                                                </div>

                                                <div class="form-row mt-2">

                                                    <div class="input-group">

                                                        <div class="custom-file">

                                                            <input type="file"
                                                                   class="custom-file-input"
                                                                   name="resume"
                                                                   id="resumeFile">

                                                            <label class="custom-file-label"
                                                                   for="resumeFile">

                                                                انتخاب فایل

                                                            </label>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                            {{-- توضیح مدیر در صورت رد درخواست --}}
                                            @if($sellerRequest && $sellerRequest->admin_note)

                                                <div class="col-md-12">

                                                    <div class="alert alert-warning">

                                                        <strong>پیام مدیریت:</strong>

                                                        <br>

                                                        {{ $sellerRequest->admin_note }}

                                                    </div>

                                                </div>

                                            @endif

                                        </div>

                                        <div class="dt-sl">

                                            <div class="form-row mt-3 justify-content-end">

                                                <button type="submit"
                                                        class="btn-primary-cm btn-with-icon ml-2">

                                                    <i class="mdi mdi-account-check-outline"></i>

                                                    ارسال درخواست همکاری

                                                </button>

                                            </div>

                                        </div>

                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Content -->
            </div>
            <!-- Start Product-Slider -->
            <section class="slider-section dt-sl mt-5 mb-5">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title text-sm-title title-wide no-after-title-wide">
                            <h2>محصولات پیشنهادی برای شما</h2>
                            <a href="#">مشاهده همه</a>
                        </div>
                    </div>

                    <!-- Start Product-Slider -->
                    <div class="col-12 px-res-0">
                        <div class="product-carousel carousel-lg owl-carousel owl-theme">
                            <div class="item">
                                <div class="product-card mb-3">
                                    <div class="product-head">
                                        <div class="rating-stars">
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                        </div>
                                        <div class="discount">
                                            <span>20%</span>
                                        </div>
                                    </div>
                                    <a class="product-thumb" href="shop-single.html">
                                        <img src="./assets/img/products/07.jpg" alt="Product Thumbnail">
                                    </a>
                                    <div class="product-card-body">
                                        <h5 class="product-title">
                                            <a href="shop-single.html">مانتو زنانه</a>
                                        </h5>
                                        <a class="product-meta" href="shop-categories.html">لباس زنانه</a>
                                        <span class="product-price">157,000 تومان</span>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="product-card mb-3">
                                    <div class="product-head">
                                        <div class="rating-stars">
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                        </div>
                                    </div>
                                    <a class="product-thumb" href="shop-single.html">
                                        <img src="./assets/img/products/017.jpg" alt="Product Thumbnail">
                                    </a>
                                    <div class="product-card-body">
                                        <h5 class="product-title">
                                            <a href="shop-single.html">کت مردانه</a>
                                        </h5>
                                        <a class="product-meta" href="shop-categories.html">لباس مردانه</a>
                                        <span class="product-price">199,000 تومان</span>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="product-card mb-3">
                                    <div class="product-head">
                                        <div class="rating-stars">
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star"></i>
                                        </div>
                                    </div>
                                    <a class="product-thumb" href="shop-single.html">
                                        <img src="./assets/img/products/013.jpg" alt="Product Thumbnail">
                                    </a>
                                    <div class="product-card-body">
                                        <h5 class="product-title">
                                            <a href="shop-single.html">مانتو زنانه مدل هودی تیک تین</a>
                                        </h5>
                                        <a class="product-meta" href="shop-categories.html">لباس زنانه</a>
                                        <span class="product-price">135,000 تومان</span>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="product-card mb-3">
                                    <div class="product-head">
                                        <div class="rating-stars">
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star"></i>
                                        </div>
                                    </div>
                                    <a class="product-thumb" href="shop-single.html">
                                        <img src="./assets/img/products/09.jpg" alt="Product Thumbnail">
                                    </a>
                                    <div class="product-card-body">
                                        <h5 class="product-title">
                                            <a href="shop-single.html">مانتو زنانه</a>
                                        </h5>
                                        <a class="product-meta" href="shop-categories.html">لباس زنانه</a>
                                        <span class="product-price">145,000 تومان</span>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="product-card mb-3">
                                    <div class="product-head">
                                        <div class="rating-stars">
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                        </div>
                                    </div>
                                    <a class="product-thumb" href="shop-single.html">
                                        <img src="./assets/img/products/010.jpg" alt="Product Thumbnail">
                                    </a>
                                    <div class="product-card-body">
                                        <h5 class="product-title">
                                            <a href="shop-single.html">مانتو زنانه</a>
                                        </h5>
                                        <a class="product-meta" href="shop-categories.html">لباس زنانه</a>
                                        <span class="product-price">170,000 تومان</span>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="product-card mb-3">
                                    <div class="product-head">
                                        <div class="rating-stars">
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star"></i>
                                        </div>
                                        <div class="discount">
                                            <span>20%</span>
                                        </div>
                                    </div>
                                    <a class="product-thumb" href="shop-single.html">
                                        <img src="./assets/img/products/011.jpg" alt="Product Thumbnail">
                                    </a>
                                    <div class="product-card-body">
                                        <h5 class="product-title">
                                            <a href="shop-single.html">مانتو زنانه</a>
                                        </h5>
                                        <a class="product-meta" href="shop-categories.html">لباس زنانه</a>
                                        <span class="product-price">185,000 تومان</span>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="product-card mb-3">
                                    <div class="product-head">
                                        <div class="rating-stars">
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star active"></i>
                                            <i class="mdi mdi-star"></i>
                                        </div>
                                    </div>
                                    <a class="product-thumb" href="shop-single.html">
                                        <img src="./assets/img/products/019.jpg" alt="Product Thumbnail">
                                    </a>
                                    <div class="product-card-body">
                                        <h5 class="product-title">
                                            <a href="shop-single.html">تیشرت مردانه</a>
                                        </h5>
                                        <a class="product-meta" href="shop-categories.html">لباس مردانه</a>
                                        <span class="product-price">54,000 تومان</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Product-Slider -->

                </div>
            </section>
            <!-- End Product-Slider -->
        </div>
    </main>
@endsection
