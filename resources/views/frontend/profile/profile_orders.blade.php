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
                        <div class="col-12">
                            <div
                                class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                                <h2>همه سفارش‌ها</h2>
                            </div>
                            <livewire:frontend.profiles.order-list/>
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
