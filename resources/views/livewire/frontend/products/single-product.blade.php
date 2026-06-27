@php
    // پیدا کردن زمان انقضای کمپین اختصاصی این محصول برای کارکرد تایمر
    $productCampaignReal = \App\Models\DiscountCampaignTarget::where('target_id', $product->id)
        ->whereHas('campaign', fn($q) => $q->where('type', 'product'))
        ->first()?->campaign;

    $expirationDate = $productCampaignReal ? $productCampaignReal->expires_at : null;
@endphp

<div class="container py-4 mx-auto single-product-wrapper">
    {{-- 🎨 بخش جامع استایل‌ها و انیمیشن‌های اختصاصی بابی شاپ با تم بنفش ملوکانه --}}

    {{-- نوار آدرس‌دهی (Breadcrumb) --}}
    <nav class="mb-4 text-sm text-muted" aria-label="breadcrumb">
        <ol class="p-0 bg-transparent d-flex list-unstyled">
            <li class="breadcrumb-item"><a href="{{route('home')}}" class="text-secondary text-decoration-none">خانه</a>
            </li>
            <li class="breadcrumb-item"><a href="#"
                                           class="text-secondary text-decoration-none">{{ $product->category?->name }}</a>
            </li>
            <li class="breadcrumb-item active text-dark font-weight-bold"
                aria-current="page">{{ Str::limit($product->name, 40) }}</li>
        </ol>
    </nav>
    {{-- 🛠️ اصلاح تداخل: بج شگفت‌انگیز به بالا سمت چپ (end-0) منتقل شد تا با دکمه علاقه‌مندی تداخل نداشته باشد --}}
    @if($expirationDate)
        <div class="position-absolute top-0 end-0 z-3 p-2" style="z-index: 10;">
                            <span
                                class="badge bg-danger font-12 px-3 py-2 rounded-pill shadow-sm font-weight-bold animate-pulse">
                                <i class="mdi mdi-fire me-1"></i>شگفت‌انگیز بابی شاپ
                            </span>
        </div>
    @endif

    <div class="p-4 border-0 shadow-sm card dt-sn rounded-20 bg-white">

        <div class="row g-4">

            {{-- بخش راست: گالری تصاویر محصول --}}
            <div class="col-lg-4 col-md-5">
                <div class="position-relative product-gallery-container">

                    {{-- دکمه علاقه‌مندی در بالا سمت راست --}}
                    <div class="position-absolute top-0 start-0 z-3 p-2 d-flex flex-column gap-2" style="z-index: 10;">
                        <button
                            wire:click="AddFavorite({{ $product->id }})"
                            class="btn btn-white shadow-sm rounded-circle d-flex align-items-center justify-content-center border"
                            style="width: 44px; height: 44px; transition: all 0.3s; background: #fff;"
                            title="افزودن به علاقه‌مندی"
                        >
                            @if($isFavorite)
                                <i class="mdi mdi-heart text-danger" style="font-size: 22px;"></i>
                            @else
                                <i class="mdi mdi-heart-outline text-muted" style="font-size: 22px;"></i>
                            @endif
                        </button>

                        @if(session()->has('message') && !str_contains(session('message'), 'سبد'))
                            <div class="bg-white text-danger border shadow-sm p-2 rounded-10 font-12 text-center mt-1"
                                 style="min-width: 150px;">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>

                    <div class="product-main-image-box border rounded-15 overflow-hidden p-3 bg-light text-center"
                         wire:ignore>
                        <div class="product-carousel owl-carousel" data-slider-id="1">
                            @foreach($product->galleries as $gallery)
                                <div class="item">
                                    <a href="{{ url('images/products/big/'.$gallery->image) }}"
                                       data-fancybox="gallery-product">
                                        <img src="{{ url('images/products/big/'.$gallery->image) }}"
                                             class="img-fluid object-fit-contain" style="max-height: 380px;"
                                             alt="{{ $product->name }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-center" wire:ignore>
                        <ul class="product-thumbnails owl-thumbs d-flex gap-2 list-unstyled overflow-x-auto p-1"
                            data-slider-id="1">
                            @foreach($product->galleries as $gallery)
                                <li class="owl-thumb-item border rounded-10 p-1 bg-white cursor-pointer transition-all {{ $loop->first ? 'active border-primary' : '' }}"
                                    style="width: 65px; height: 65px;">
                                    <img src="{{ url('images/products/big/'.$gallery->image) }}"
                                         class="w-100 h-100 object-fit-cover rounded-8" alt="بندانگشتی">
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- بخش وسط: مشخصات و عنوان محصول --}}
            <div class="col-lg-5 col-md-7 border-start-md px-lg-4">
                <div class="product-details-content">
                    <h1 class="h4 text-dark font-weight-black lh-base mb-2">{{ $product->name }}</h1>
                    @if($product->e_name)
                        <h2 class="text-muted font-numeric mb-4"
                            style="font-size: 15px; letter-spacing: 0.5px;">{{ $product->e_name }}</h2>
                    @endif

                    <hr class="opacity-50 my-3">

                    @if(session()->has('message') && str_contains(session('message'), 'سبد'))
                        <div
                            class="alert alert-success py-2 px-3 border-0 rounded-10 font-13 mb-3 d-flex align-items-center">
                            <i class="mdi mdi-check-circle-outline me-2 text-success fs-5"></i>
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="product-specifications-short">
                        <h5 class="font-15 text-secondary font-weight-bold mb-3">
                            <i class="mdi mdi-format-list-bulleted-type text-primary me-1"></i> ویژگی‌های مهم محصول:
                        </h5>
                        <ul class="list-unstyled p-0 m-0 d-flex flex-column gap-2">
                            @foreach($product->propertyGroups as $propertyGroup)
                                @php
                                    $propertiesList = $propertyGroup->properties->where('product_id', $product->id)->pluck('name')->implode('، ');
                                @endphp
                                @if(filled($propertiesList))
                                    <li class="d-flex align-items-baseline font-14 py-1"
                                        wire:key="spec-{{ $propertyGroup->id }}">
                                        <span class="text-muted text-nowrap me-2" style="width: 120px;">• {{ $propertyGroup->name }}:</span>
                                        <strong class="text-dark-75 font-weight-semibold">{{ $propertiesList }}</strong>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <div class="row g-2 mt-4 text-center product-trust-icons">
                        <div class="col-4">
                            <div class="p-2 bg-light rounded-12 border-0">
                                <i class="mdi mdi-shield-check-outline text-success fs-3 mb-1 d-block"></i>
                                <span class="font-11 text-muted font-weight-bold">اصالت تضمینی</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded-12 border-0">
                                <i class="mdi mdi-flash-outline text-warning fs-3 mb-1 d-block"></i>
                                <span class="font-11 text-muted font-weight-bold">تحویل آنی فایل</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded-12 border-0">
                                <i class="mdi mdi-headphones text-info fs-3 mb-1 d-block"></i>
                                <span class="font-11 text-muted font-weight-bold">پشتیبانی درصورت بروز مشکل</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- بخش چپ: سایدبار باکس خرید و تایمر معکوس --}}
            <div class="col-lg-3 col-md-12">
                <div class="p-3 border rounded-20 bg-light-sidebar sticky-top-card" style="top: 20px;">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom font-14 text-dark-75">
                        <i class="mdi mdi-storefront-outline text-primary fs-4 me-2"></i>
                        <div>
                            <span class="d-block font-weight-bold">فروشگاه: بابی شاپ</span>
                            <small class="text-success font-12"><i class="mdi mdi-check-decagram"></i>
                                فروشنده : رابی گرافیک</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3 font-14 text-dark-75">
                        <i class="mdi mdi-check-circle-outline text-success fs-4 me-2"></i>
                        <div>
                            <span class="d-block font-weight-semibold">وضعیت کالا: موجود</span>
                            <small class="text-muted font-11">آماده دانلود / تحویل آنلاین دیجیتال</small>
                        </div>
                    </div>

                    {{-- 🛠️ اصلاح راست‌چین: استفاده از flex-row-reverse جهت نمایش مرتب و اصولی روز، ساعت، دقیقه و ثانیه از راست به چپ --}}
                    @if($expirationDate)
                        <div
                            class="mb-3 p-3 text-white rounded-15 animate-pulse d-flex flex-column gap-2 border-0 shadow-sm text-center"
                            style="background: linear-gradient(135deg, #ef4056 0%, #c31432 100%);" wire:ignore>
                            <div
                                class="d-flex justify-content-center align-items-center gap-1 font-13 font-weight-bold">
                                <i class="mdi mdi-clock-flash fs-5"></i>
                                <span>فرصت محدود پیشنهاد شگفت‌انگیز!</span>
                            </div>
                            <div
                                class="babi-custom-timer d-flex flex-row-reverse gap-2 font-numeric align-items-center justify-content-center text-white mt-1"
                                id="babi-product-countdown"
                                data-expiration="{{ $expirationDate }}">

                                <div
                                    class="d-flex flex-column align-items-center bg-dark bg-opacity-25 rounded-8 px-2 py-1"
                                    style="min-width: 42px;">
                                    <span class="h6 font-weight-black mb-0 text-white" id="babi-days">00</span>
                                    <small style="font-size: 9px; opacity: 0.85;">روز</small>
                                </div>
                                <span class="font-weight-black" style="line-height: 1.2; padding-bottom: 12px;">:</span>
                                <div
                                    class="d-flex flex-column align-items-center bg-dark bg-opacity-25 rounded-8 px-2 py-1"
                                    style="min-width: 42px;">
                                    <span class="h6 font-weight-black mb-0 text-white" id="babi-hours">00</span>
                                    <small style="font-size: 9px; opacity: 0.85;">ساعت</small>
                                </div>
                                <span class="font-weight-black" style="line-height: 1.2; padding-bottom: 12px;">:</span>
                                <div
                                    class="d-flex flex-column align-items-center bg-dark bg-opacity-25 rounded-8 px-2 py-1"
                                    style="min-width: 42px;">
                                    <span class="h6 font-weight-black mb-0 text-white" id="babi-minutes">00</span>
                                    <small style="font-size: 9px; opacity: 0.85;">دقیقه</small>
                                </div>
                                <span class="font-weight-black" style="line-height: 1.2; padding-bottom: 12px;">:</span>
                                <div
                                    class="d-flex flex-column align-items-center bg-dark bg-opacity-25 rounded-8 px-2 py-1"
                                    style="min-width: 42px;">
                                    <span class="h6 font-weight-black mb-0 text-white" id="babi-seconds">00</span>
                                    <small style="font-size: 9px; opacity: 0.85;">ثانیه</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="price-section-box mb-4 p-3 bg-white rounded-15 border-0">
                        @if($product->discount_percent > 0)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted font-13"><del
                                        class="font-numeric">{{ number_format($product->main_price) }}</del></span>
                                <span class="badge bg-danger rounded-pill font-numeric font-12 px-2 py-1">{{ $product->discount_percent }}٪ تخفیف</span>
                            </div>
                        @endif

                        <div class="d-flex align-items-baseline justify-content-between mt-2">
                            <span class="text-secondary font-13 font-weight-bold">قیمت نهایی:</span>
                            <div>
                                <span class="h3 font-weight-black font-numeric mb-0"
                                      style="color: #8b5cf6;">{{ number_format($product->final_price) }}</span>
                                <small class="text-muted ms-1 font-11">تومان</small>
                            </div>
                        </div>

                        @if($product->main_price > $product->final_price)
                            <div
                                class="text-center mt-2 pt-2 border-top border-dashed font-12 text-danger font-numeric">
                                <i class="mdi mdi-tag-heart-outline"></i> سود شما از خرید:
                                <span>{{ number_format($product->main_price - $product->final_price) }}</span> تومان
                            </div>
                        @endif
                    </div>

                    <button
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        class="btn btn-lg w-100 rounded-15 py-3 font-weight-bold shadow-sm d-flex align-items-center justify-content-center transition-all call-to-action-btn-purple"
                    >
                        <span wire:loading.remove wire:target="addToCart" class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-cart-plus fs-4"></i> افزودن به سبد خرید
                        </span>

                        <span wire:loading wire:target="addToCart" class="align-items-center gap-2"
                              style="display: none;">
                            <span class="spinner-border spinner-border-sm text-white" role="status"
                                  aria-hidden="true"></span>
                            در حال انتقال به سبد...
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
