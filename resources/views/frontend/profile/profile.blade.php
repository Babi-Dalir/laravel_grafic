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
                                <div
                                    class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                                    <h2>ویرایش اطلاعات شخصی</h2>
                                </div>
                                <div class="form-ui additional-info dt-sl dt-sn pt-4">
                                    <form action="{{route('profile.update')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>نام</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text" class="input-ui pr-2" name="name"
                                                           placeholder="نام خود را وارد نمایید" value="{{$user->name}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>نام کاربری</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text" class="input-ui pr-2" name="user_name"
                                                           placeholder="نام کاربری خود را وارد نمایید"
                                                           value="{{$user->user_name}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>تلفن تماس</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text"
                                                           class="input-ui pl-2 text-left dir-ltr"
                                                           name="phone"
                                                           placeholder="09123456789"
                                                           value="{{ $user->userProfile?->phone }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>تلگرام</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text"
                                                           class="input-ui pl-2 text-left dir-ltr"
                                                           name="telegram"
                                                           value="{{ $user->userProfile?->telegram }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>ایتا</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text"
                                                           class="input-ui pl-2 text-left dir-ltr"
                                                           name="eta"
                                                           value="{{ $user->userProfile?->eta }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>اینستاگرام</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text"
                                                           class="input-ui pl-2 text-left dir-ltr"
                                                           name="instagram"
                                                           value="{{ $user->userProfile?->instagram }}">
                                                </div>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <div class="form-row-title">
                                                    <h3>وب سایت</h3>
                                                </div>
                                                <div class="form-row">
                                                    <input type="text"
                                                           class="input-ui pl-2 text-left dir-ltr"
                                                           name="website"
                                                           placeholder="https://www.example.com"
                                                           value="{{ $user->userProfile?->website }}">
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="form-row-title">
                                                    <h3>درباره من</h3>
                                                </div>
                                                <div class="form-row">
                                            <textarea
                                                class="input-ui pt-2"
                                                name="bio"
                                                rows="5"
                                                placeholder="خودتان، تخصص‌ها و سبک طراحی‌تان را معرفی کنید...">{{ $user->userProfile?->bio }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-row-title">
                                                    <h3>عکس پروفایل</h3>
                                                </div>
                                                <div class="form-row mt-2">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="image"
                                                                   id="inputGroupFile04"
                                                                   aria-describedby="inputGroupFileAddon04">
                                                            <label class="custom-file-label"
                                                                   for="inputGroupFile04">انتخاب
                                                                عکس</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dt-sl">
                                            <div class="form-row mt-3 justify-content-end">
                                                <button type="submit" class="btn-primary-cm btn-with-icon ml-2">
                                                    <i class="mdi mdi-account-circle-outline"></i>
                                                    ثبت اطلاعات کاربری
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
            <section class="dt-sl dt-sn mb-5">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title text-sm-title title-wide no-after-title-wide">
                            <h2>پیشنهاد ما</h2>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    @foreach ($instant_offers->chunk(3) as $products)
                        @foreach ($products as $product)
                            <div class="col-lg-4 col-md-6 col-sm-12 pt-4">
                                <div class="card-horizontal-product border-bottom rounded-0">
                                    <div class="card-horizontal-product-thumb position-relative"> {{-- اضافه شدن پوزیشن نسبی --}}
                                        <a href="{{ route('single.product', $product->slug) }}">
                                            <img src="{{ url('images/products/big/' . $product->image) }}"
                                                 alt="{{ $product->name }}">
                                        </a>

                                        {{-- نشان تخفیف قرمز و استایلی --}}
                                        @if($product->discount_percent > 0)
                                            <div class="discount-badge-red">
                                                <span>{{ $product->discount_percent }}%</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-horizontal-product-content">
                                        <div class="card-horizontal-product-title">
                                            <a href="{{ route('single.product', $product->slug) }}">
                                                <h3>{{ $product->name }}</h3>
                                            </a>
                                        </div>

                                        <div class="card-horizontal-product-price">
                                            @if($product->hasDiscount())
                                                <del class="text-secondary small d-block">{{ number_format($product->main_price) }}</del>
                                                <span class="text-danger font-weight-bold" style="font-size: 1.1rem;">
                                    {{ number_format($product->final_price) }} <small>تومان</small>
                                </span>
                                            @else
                                                <span class="font-weight-bold">{{ number_format($product->main_price) }} تومان</span>
                                            @endif
                                        </div>

                                        <div class="card-horizontal-product-buttons mt-2">
                                            <a href="{{ route('single.product', $product->slug) }}"
                                               class="btn btn-sm btn-outline-info">مشاهده محصول</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </section>
            <!-- End Product-Slider -->
        </div>
    </main>
@endsection
