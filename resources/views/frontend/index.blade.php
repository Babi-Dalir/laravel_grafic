@extends('frontend.layouts.master')
@section('content')
    @include('frontend.layouts.header')
    <main class="main-content dt-sl mb-3">
        <div class="container main-container">
            <!-- Start Main-Slider -->
            <div class="row mb-3">
                <aside class="sidebar col-xl-2 col-lg-3 col-12 order-2 order-lg-1 pl-0 hidden-md">
                    <!-- Start banner -->
                    <div class="sidebar-inner dt-sl">
                        <div class="sidebar-banner">
                            <a href="#" target="_top">
                                <img
                                    src="{{ url('images/banners/big/' . $banners->where('type', 'side_banner')->first()->image) }}"
                                    width="100%" height="329" alt="">
                            </a>
                        </div>
                    </div>
                    <!-- End banner -->
                </aside>
                <div class="col-xl-10 col-lg-9 col-12 order-1 order-lg-2">
                    <!-- Start main-slider -->
                    <section id="main-slider" class="main-slider carousel slide carousel-fade card hidden-sm"
                             data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach ($sliders as $slider)
                                <li data-target="#main-slider" data-slide-to="{{ $slider->id }}"
                                    @if ($loop->first) class="active" @endif></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach ($sliders as $slider)
                                <div class="carousel-item @if ($loop->first) active @endif">
                                    <a class="main-slider-slide" href="{{$slider->link}}"
                                       style="background-image: url({{ url('images/sliders/big/' . $slider->image) }})">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#main-slider" role="button" data-slide="prev">
                            <i class="mdi mdi-chevron-right"></i>
                        </a>
                        <a class="carousel-control-next" href="#main-slider" data-slide="next">
                            <i class="mdi mdi-chevron-left"></i>
                        </a>
                    </section>
                    <section id="main-slider-res" class="main-slider carousel slide carousel-fade card d-none show-sm"
                             data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach ($sliders as $slider)
                                <li data-target="#main-slider-res" data-slide-to="{{ $slider->id }}"
                                    @if ($loop->first) class="active" @endif></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach ($sliders as $slider)
                                <div class="carousel-item @if ($loop->first) active @endif ">
                                    <a class="main-slider-slide" href="#">
                                        <img src="{{ url('images/sliders/big/' . $slider->image) }}" alt=""
                                             class="img-fluid">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#main-slider-res" role="button" data-slide="prev">
                            <i class="mdi mdi-chevron-right"></i>
                        </a>
                        <a class="carousel-control-next" href="#main-slider-res" data-slide="next">
                            <i class="mdi mdi-chevron-left"></i>
                        </a>
                    </section>
                    <!-- End main-slider -->
                </div>
            </div>
            <!-- End Main-Slider -->
            <!-- Start Category-Section -->
            <div class="row mt-4 mb-5">
                <div class="col-12">
                    <div class="category-section dt-sn dt-sl border" style="padding: 30px 20px; background: #ffffff; border-radius: 26px; position: relative; overflow: hidden;">

                        <div class="section-title text-sm-title title-wide no-after-title-wide mb-4">
                            <h2 style="font-size: 18.5px; font-weight: 850; color: #0f172a; display: flex; align-items: center; gap: 12px;">
                                <span class="grafic-neon-pulse-dot"></span>
                                دسته‌بندی‌ها
                            </h2>
                        </div>

                        <div class="row g-4 px-2">
                            @foreach ($categories as $category)
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                                    <div class="grafic-future-neon-box">

                                        <div class="neon-plasma-glow"></div>

                                        <a href="{{ route('main.category.product.list', $category->slug) }}" class="neon-box-anchor">

                                            <div class="neon-icon-core-wrapper">
                                                <div class="neon-loading-ring-flow"></div>
                                                <div class="neon-icon-shield">
                                                    <img src="{{ url('images/categories/big/' . $category->image) }}" alt="{{ $category->name }}">
                                                </div>
                                            </div>

                                            <div class="neon-box-details">
                                                <h4 class="neon-box-title">{{ $category->name }}</h4>
                                                <span class="neon-box-status font-numeric">
                                        <span class="neon-online-indicator"></span>
                                        {{ $category->products_count }} منبع دیجیتال
                                    </span>
                                            </div>

                                            <div class="neon-box-action-btn">
                                                <i class="mdi mdi-arrow-left"></i>
                                            </div>
                                        </a>

                                        <div class="neon-box-footer-tags">
                                            @if($category->childCategory && $category->childCategory->count() > 0)
                                                @foreach($category->childCategory->take(2) as $subCategory)
                                                    <a href="{{ route('main.category.product.list', $subCategory->slug) }}">
                                                        {{ $subCategory->name }}
                                                    </a>
                                                @endforeach
                                            @else
                                                <span class="neon-static-tag-prime">پریمیوم</span>
                                                <span class="neon-static-tag-prime">وکتور کات</span>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Category-Section -->
            <!-- Start Product-Slider -->
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <section class="slider-section dt-sl mb-5">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="section-title text-sm-title title-wide no-after-title-wide">
                                    <h2>پر فروش ترینها</h2>
                                </div>
                            </div>

                            <!-- Start Product-Slider -->
                            <div class="col-12 px-res-0">
                                <div class="product-carousel carousel-md owl-carousel owl-theme">
                                    @foreach ($most_sold as $product)
                                        <div class="item">
                                            <div class="product-card">
                                                <div class="product-head">
                                                    <div class="rating-stars">

                                                    </div>
                                                    @if($product->discount_percent > 0)
                                                        <div class="discount">
                                                            <span>{{ $product->discount_percent }}%</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <a class="product-thumb"
                                                   href="{{ route('single.product', $product->slug) }}">
                                                    <img src="{{ url('images/products/big/' . $product->image) }}"
                                                         alt="Product Thumbnail">
                                                </a>
                                                <div class="product-card-body">
                                                    <h5 class="product-title">
                                                        <a
                                                            href="{{ route('single.product', $product->slug) }}">{{ $product->name }}</a>
                                                    </h5>
                                                    <a class="product-meta"
                                                       href="#">{{ $product->category->name }}</a>
                                                    @if($product->hasDiscount())
                                                        <del
                                                            class="text-danger small">{{ number_format($product->main_price) }}</del>
                                                        <span class="product-price">{{ number_format($product->final_price) }} تومان</span>
                                                    @else
                                                        <span class="product-price">{{ number_format($product->main_price) }} تومان</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- End Product-Slider -->
                        </div>
                    </section>
                </div>
            </div>
            <!-- End Product-Slider -->
            <!-- Start Banner -->
            <div class="row mt-3 mb-5">
                @foreach ($banners->where('type', 'medium_banner') as $medium_banner)
                    <div class="col-sm-6 col-12 mb-2">
                        <div class="widget-banner">
                            <a href="#">
                                <img src="{{ url('images/banners/big/' . $medium_banner->image) }}" alt="">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- End Banner -->
            <!-- Start Banner -->
            <div class="row mt-3 mb-5">
                @foreach ($banners->where('type', 'small_banner') as $small_banner)
                    <div class="col-md-3 col-sm-6 col-6 mb-2">
                        <div class="widget-banner">
                            <a href="#">
                                <img src="{{ url('images/banners/big/' . $small_banner->image) }}" alt="">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- End Banner -->
            <!-- Start Product-Slider -->
            <section class="slider-section dt-sl mb-5">
                <div class="row mb-3">
                    @if ($spacial_products->count() > 0)
                        <div class="col-12">
                            <div class="section-title text-sm-title title-wide no-after-title-wide">
                                <h2>فروش شگفت انگیز</h2>
                            </div>
                        </div>
                    @endif
                    <!-- Start Product-Slider -->
                    <div class="col-12">
                        <div class="product-carousel carousel-lg owl-carousel owl-theme">
                            @foreach ($spacial_products as $spacial_product)
                                <div class="item">
                                    <div class="product-card">
                                        {{-- بخش بالایی کارت شامل ستاره‌ها و تخفیف --}}
                                        <div class="product-head">
                                            <div class="rating-stars">

                                            </div>
                                            {{-- استفاده از اکسسوری که در مدل محصول ساختیم --}}
                                            @if($spacial_product->discount_percent > 0)
                                                <div class="discount">
                                                    <span>{{ $spacial_product->discount_percent }}%</span>
                                                </div>
                                            @endif
                                        </div>

                                        <a class="product-thumb" href="{{ route('single.product', $spacial_product->slug) }}">
                                            <img src="{{ url('images/products/big/' . $spacial_product->image) }}" alt="{{ $spacial_product->name }}">
                                        </a>

                                        <div class="product-card-body">
                                            <h5 class="product-title">
                                                <a href="{{ route('single.product', $spacial_product->slug) }}">{{ $spacial_product->name }}</a>
                                            </h5>
                                            <a class="product-meta"
                                               href="#">{{ $spacial_product->category->name }}</a>
                                            {{-- قیمت خط خورده و قیمت نهایی --}}
                                            <div class="product-price-info">
                                                <del class="text-danger small">{{ number_format($spacial_product->main_price) }}</del>
                                                <span class="product-price d-block">{{ number_format($spacial_product->final_price) }} تومان</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- End Product-Slider -->

                </div>
            </section>
            <!-- End Product-Slider -->
            <!-- Start Banner -->
            <div class="row mt-3 mb-5">
                <div class="col-12">
                    <div class="widget-banner">
                        <a href="#">
                            <img
                                src="{{ url('images/banners/big/' . $banners->where('type', 'large_banner')->first()->image) }}"
                                alt="">
                        </a>
                    </div>
                </div>
            </div>
            <!-- End Banner -->
            <!-- Start Product-Slider -->
            <section class="slider-section dt-sl mb-5">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title text-sm-title title-wide no-after-title-wide">
                            <h2>جدید ترینها</h2>
                        </div>
                    </div>

                    <!-- Start Product-Slider -->
                    <div class="col-12">
                        <div class="product-carousel carousel-lg owl-carousel owl-theme">
                            @foreach ($newest_products as $newest_product)
                                <div class="item">
                                    <div class="product-card">
                                        <div class="product-head">
                                            <div class="rating-stars">

                                            </div>
                                            @if($newest_product->discount_percent > 0)
                                                <div class="discount">
                                                    <span>{{ $newest_product->discount_percent }}%</span>
                                                </div>
                                            @endif
                                        </div>
                                        <a class="product-thumb"
                                           href="{{ route('single.product', $newest_product->slug) }}">
                                            <img src="{{ url('images/products/big/' . $newest_product->image) }}"
                                                 alt="Product Thumbnail">
                                        </a>
                                        <div class="product-card-body">
                                            <h5 class="product-title">
                                                <a
                                                    href="{{ route('single.product', $newest_product->slug) }}">{{ $newest_product->name }}</a>
                                            </h5>
                                            <a class="product-meta"
                                               href="#">{{ $newest_product->category->name }}</a>
                                            <div class="product-price">
                                                @if($newest_product->hasDiscount())
                                                    <del
                                                        class="text-danger small">{{ number_format($newest_product->main_price) }}</del>
                                                    <span class="d-block">{{ number_format($newest_product->final_price) }} تومان</span>
                                                @else
                                                    <span>{{ number_format($newest_product->main_price) }} تومان</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- End Product-Slider -->

                </div>
            </section>
            <!-- End Product-Slider -->
            <!-- Start Feature-Product -->
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

            <!-- End Feature-Product -->
        </div>
    </main>

    {{-- 🌌 نسخه نهایی و فوق‌پریمیوم Aurora Hologram با تفکیک نوع کمپین (پایین سمت چپ) 🌌 --}}
    @if(isset($activeDiscountCampaign) && $activeDiscountCampaign->expires_at)
        <div id="grafic-ultimate-widget" class="grafic-aurora-widget" style="display: none;">

            <div class="aurora-blur-layer"></div>
            <div class="aurora-shine-streak"></div>

            <button type="button" class="aurora-close-btn" onclick="closeGraficUltimateWidget()" aria-label="بستن">
                <i class="mdi mdi-close"></i>
            </button>

            <a href="#" class="aurora-widget-anchor">
                <div class="aurora-badge-capsule">
                    <div class="capsule-glow"></div>
                    <div class="capsule-content">
                        <span class="font-numeric">{{ $activeDiscountCampaign->percent }}%</span>
                        <small>OFF</small>
                    </div>
                </div>

                <div class="aurora-widget-body">
                    <div class="aurora-meta-tag">
                        @if($activeDiscountCampaign->type === \App\Enums\DiscountCampaignType::Global->value)
                            {{-- 🌐 حالت کل سایت --}}
                            <span class="tag-badge tag-global">
                            <span class="pulse-indicator pulse-cyan"></span>
                            جشنواره سراسری کل سایت
                        </span>
                        @else
                            {{-- 📁 حالت دسته‌بندی خاص --}}
                            <span class="tag-badge tag-category">
                            <span class="pulse-indicator pulse-purple"></span>
                            تخفیف ویژه این دسته‌بندی
                        </span>
                        @endif
                    </div>

                    <h4 class="aurora-widget-title">{{ $activeDiscountCampaign->name }}</h4>

                    <div class="aurora-countdown-wrapper" dir="ltr">
                        <div class="time-block"><span id="u-days" class="font-numeric">00</span><small>روز</small></div>
                        <span class="time-divider">:</span>
                        <div class="time-block"><span id="u-hours" class="font-numeric">00</span><small>ساعت</small></div>
                        <span class="time-divider">:</span>
                        <div class="time-block"><span id="u-minutes" class="font-numeric">00</span><small>دقیقه</small></div>
                        <span class="time-divider">:</span>
                        <div class="time-block"><span id="u-seconds" class="font-numeric">00</span><small>ثانیه</small></div>
                    </div>
                </div>

                <div class="aurora-arrow-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </div>
            </a>
        </div>

        <script>
            function closeGraficUltimateWidget() {
                const widget = document.getElementById('grafic-ultimate-widget');
                widget.classList.add('aurora-widget-exit');
                setTimeout(() => widget.remove(), 500);
            }

            document.addEventListener("DOMContentLoaded", function () {
                const targetTimestamp = new Date("{{ $activeDiscountCampaign->expires_at }}").getTime();
                const widgetNode = document.getElementById("grafic-ultimate-widget");

                const dNode = document.getElementById("u-days");
                const hNode = document.getElementById("u-hours");
                const mNode = document.getElementById("u-minutes");
                const sNode = document.getElementById("u-seconds");

                function updateUltimateTimer() {
                    const now = new Date().getTime();
                    const remainder = targetTimestamp - now;

                    if (remainder < 0) {
                        closeGraficUltimateWidget();
                        clearInterval(ultimateInterval);
                        return;
                    }

                    const days = Math.floor(remainder / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((remainder % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((remainder % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((remainder % (1000 * 60)) / 1000);

                    dNode.innerText = String(days).padStart(2, '0');
                    hNode.innerText = String(hours).padStart(2, '0');
                    mNode.innerText = String(minutes).padStart(2, '0');
                    sNode.innerText = String(seconds).padStart(2, '0');

                    if (widgetNode.style.display === "none") {
                        widgetNode.style.display = "flex";
                    }
                }

                updateUltimateTimer();
                const ultimateInterval = setInterval(updateUltimateTimer, 1000);
            });
        </script>
    @endif
@endsection
