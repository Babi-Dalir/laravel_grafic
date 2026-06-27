<div>
    @if($carts->isEmpty() && $reserve_carts->isEmpty())
        <div class="dt sl dt-sn dt-sn--box border pt-3 pb-5 rounded-20 bg-white">
            <div class="cart-page cart-empty">
                <div class="circle-box-icon">
                    <i class="mdi mdi-cart-remove"></i>
                </div>
                <p class="cart-empty-title font-weight-black">سبد خرید شما خالیست!</p>
            </div>
        </div>
    @else
        <div class="row mx-0">
            <div class="col-xl-9 col-lg-8 col-md-12 col-sm-12 mb-2">
                <nav class="tab-cart-page">
                    <div class="nav nav-tabs border-bottom" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link d-inline-flex w-auto active font-weight-bold" id="nav-home-tab"
                           data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home"
                           aria-selected="true">سبد خرید<span class="count-cart font-numeric">{{ count($carts) }}</span></a>
                        <a class="nav-item nav-link d-inline-flex w-auto font-weight-bold" id="nav-profile-tab" data-toggle="tab"
                           href="#nav-profile" role="tab" aria-controls="nav-profile"
                           aria-selected="false">لیست خرید بعدی<span class="count-cart font-numeric">{{ count($reserve_carts) }}</span></a>
                    </div>
                </nav>
            </div>

            <div class="col-12">
                <div class="tab-content" id="nav-tabContent">
                    {{-- تب سبد خرید اصلی --}}
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="row">
                            <div class="col-xl-9 col-lg-8 col-12 px-0">
                                <div class="table-responsive checkout-content dt-sl card border-0 shadow-sm rounded-20 bg-white p-3">
                                    <div class="checkout-header checkout-header--express border-bottom pb-3 mb-2 d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="checkout-header-title font-weight-bold text-dark fs-6"><i class="mdi mdi-truck-delivery-outline text-primary me-1"></i>ارسال عادی</span>
                                            <span class="checkout-header-extra-info text-muted font-13 font-numeric">({{ count($carts) }} کالا)</span>
                                        </div>
                                        <span wire:loading class="text text-info mr-3 font-13"><span class="spinner-border spinner-border-sm me-1" role="status"></span>درحال بروزرسانی...</span>
                                    </div>
                                    <div class="checkout-section-content-dd-k">
                                        <div class="cart-items-dd-k">
                                            @foreach($carts as $cart)
                                                @if($cart->product)
                                                    <div class="cart-item py-4 px-3 border-bottom d-flex gap-3 align-items-center">
                                                        <div class="item-thumbnail border rounded-12 p-1 bg-light">
                                                            <a href="#">
                                                                <img src="{{ url('images/products/big/'.$cart->product->image) }}" class="rounded-8" style="width: 90px; height: 90px; object-fit: cover;" alt="item">
                                                            </a>
                                                        </div>
                                                        <div class="item-info flex-grow-1">
                                                            <div class="item-title mb-2">
                                                                <h2 class="h6 font-weight-bold mb-0"><a href="#" class="text-dark text-decoration-none lh-base">{{ $cart->product->name }}</a></h2>
                                                            </div>
                                                            <div class="item-detail">
                                                                <ul class="list-unstyled p-0 m-0 d-flex flex-wrap gap-x-4 gap-y-1 font-13 text-muted mb-2">
                                                                    <li class="d-flex align-items-center"><i class="mdi mdi-storefront-outline me-1"></i><span>بابی شاپ</span></li>
                                                                    <li class="d-flex align-items-center"><i class="mdi mdi-cloud-download-outline text-primary me-1"></i><span>دانلود آنی پس از پرداخت</span></li>
                                                                </ul>
                                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                                                                    <div class="item-actions d-flex align-items-center gap-2">
                                                                        <button wire:click="deleteCart({{ $cart->id }})" class="btn btn-sm btn-outline-danger rounded-8 px-2 font-12 d-flex align-items-center gap-1">
                                                                            <i class="mdi mdi-trash-can-outline"></i> حذف
                                                                        </button>
                                                                        <button wire:click="moveToReserveCart({{ $cart->id }})" class="btn btn-sm btn-outline-secondary rounded-8 px-2 font-12 d-flex align-items-center gap-1">
                                                                            <i class="mdi mdi-clock-outline"></i> بعداً می‌خرم
                                                                        </button>
                                                                    </div>
                                                                    <div class="item-price font-numeric text-dark font-weight-black fs-5">
                                                                        {{ number_format($cart->product->final_price) }} <small class="text-muted font-11 font-weight-normal ms-1">تومان</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 🌟 سایدبار فاکتور محاسبات مالی کاملاً بازسازی شده بر اساس تم لوکس بابی شاپ --}}
                            <div class="col-xl-3 col-lg-4 col-12 w-res-sidebar sticky-sidebar ps-lg-3">
                                <div class="p-3 border rounded-20 bg-light-sidebar shadow-sm mb-3">
                                    <ul class="checkout-summary-summary list-unstyled p-0 m-0 mb-3 d-flex flex-column gap-2-5 font-14">
                                        <li class="d-flex justify-content-between align-items-center text-secondary py-1">
                                            <span>مبلغ کل کالاها:</span>
                                            <span class="font-numeric text-dark font-weight-semibold">{{ number_format($total_price) }} تومان</span>
                                        </li>

                                        @if($discount_price > 0)
                                            <li class="d-flex justify-content-between align-items-center py-1">
                                                <span class="text-danger">سود شما از تخفیف:</span>
                                                <span class="font-numeric font-weight-bold text-danger">{{ number_format($discount_price) }} تومان</span>
                                            </li>
                                        @endif

                                        @if($discount_code_price > 0)
                                            <li class="d-flex justify-content-between align-items-center py-1">
                                                <span class="text-info">کد تخفیف اعمال شده:</span>
                                                <span class="font-numeric font-weight-bold text-info">{{ number_format($discount_code_price) }} - تومان</span>
                                            </li>
                                        @endif

                                        @if($gift_cart_price > 0)
                                            <li class="d-flex justify-content-between align-items-center py-1">
                                                <span class="text-info">کارت هدیه اعمال شده:</span>
                                                <span class="font-numeric font-weight-bold text-info">{{ number_format($gift_cart_price) }} - تومان</span>
                                            </li>
                                        @endif
                                    </ul>

                                    {{-- باکس شیک و تفکیک شده قیمت نهایی کالا --}}
                                    <div class="price-section-box mb-3 p-3 bg-white rounded-15 border-0">
                                        <div class="d-flex align-items-baseline justify-content-between">
                                            <span class="text-secondary font-13 font-weight-bold">مبلغ قابل پرداخت:</span>
                                            <div>
                                                <span class="h3 font-weight-black font-numeric mb-0" style="color: #a855f7;">{{ number_format($final_price) }}</span>
                                                <small class="text-muted ms-1 style-toman">تومان</small>
                                            </div>
                                        </div>
                                    </div>

                                    @if($carts->isNotEmpty())
                                        {{-- دکمه با رنگ ارغوانی ملوکانه هماهنگ با صفحه محصول --}}
                                        <button wire:click="submitPayment" class="btn btn-lg w-100 rounded-15 py-3 font-weight-bold shadow-sm d-flex align-items-center justify-content-center gap-2 call-to-action-btn-purple transition-all mt-2">
                                            <i class="mdi mdi-credit-card-outline fs-5"></i> پرداخت و ثبت نهایی سفارش
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- بخش تخفیف‌ها و کارت هدیه --}}
                        <div class="row mt-4 mx-0">
                            <div class="col-md-6 col-12 mb-3 px-1">
                                <div class="card border-0 shadow-sm rounded-20 p-4 bg-white h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="mdi mdi-gift-outline text-primary fs-4"></i>
                                        <h3 class="h6 font-weight-black text-dark mb-0">استفاده از کارت هدیه</h3>
                                    </div>
                                    <p class="mb-3 text-muted font-13">با ثبت کد کارت هدیه، مبلغ آن از فاکتور کسر می‌شود.</p>
                                    <div class="form-ui">
                                        <form wire:submit.prevent="giftCartCode">
                                            <div class="row align-items-center g-2">
                                                <div class="col-lg-8 col-md-7 col-12">
                                                    <input type="text" class="form-control rounded-12 py-2 px-3 font-numeric text-center" style="border: 1px solid #cbd5e1;" wire:model="gift_cart_code" placeholder="مثلا GIFT-XM42">
                                                </div>
                                                <div class="col-lg-4 col-md-5 col-12">
                                                    <button type="submit" class="btn btn-primary w-100 rounded-12 py-2 font-weight-bold">ثبت کد</button>
                                                </div>
                                            </div>
                                            @if(session()->has('success_gift_cart'))
                                                <div class="alert alert-success py-2 px-3 border-0 rounded-10 font-13 mt-2">{{ session('success_gift_cart') }}</div>
                                            @endif
                                            @if(session()->has('warning_gift_cart'))
                                                <div class="alert alert-danger py-2 px-3 border-0 rounded-10 font-13 mt-2">{{ session('warning_gift_cart') }}</div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-12 mb-3 px-1">
                                <div class="card border-0 shadow-sm rounded-20 p-4 bg-white h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="mdi mdi-ticket-percent-outline text-danger fs-4"></i>
                                        <h3 class="h6 font-weight-black text-dark mb-0">استفاده از کد تخفیف</h3>
                                    </div>
                                    <p class="mb-3 text-muted font-13">کد تخفیف خود را در کادر زیر وارد نمایید.</p>
                                    <div class="form-ui">
                                        <form wire:submit.prevent="discountCode">
                                            <div class="row align-items-center g-2">
                                                <div class="col-lg-8 col-md-7 col-12">
                                                    <input type="text" class="form-control rounded-12 py-2 px-3 font-numeric text-center" style="border: 1px solid #cbd5e1;" wire:model="discount_code" placeholder="مثلا OFF-1405">
                                                </div>
                                                <div class="col-lg-4 col-md-5 col-12">
                                                    <button type="submit" class="btn btn-danger w-100 rounded-12 py-2 font-weight-bold">اعمال تخفیف</button>
                                                </div>
                                            </div>
                                            @if(session()->has('success_discount'))
                                                <div class="alert alert-success py-2 px-3 border-0 rounded-10 font-13 mt-2">{{ session('success_discount') }}</div>
                                            @endif
                                            @if(session()->has('warning_discount'))
                                                <div class="alert alert-danger py-2 px-3 border-0 rounded-10 font-13 mt-2">{{ session('warning_discount') }}</div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- تب لیست خرید بعدی --}}
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="row">
                            <div class="col-xl-9 col-lg-8 col-12 px-0">
                                <div class="table-responsive checkout-content dt-sl card border-0 shadow-sm rounded-20 bg-white p-3">
                                    <div class="checkout-header checkout-header--express border-bottom pb-3 mb-2 d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="checkout-header-title font-weight-bold text-dark fs-6">لیست خرید بعدی</span>
                                            <span class="checkout-header-extra-info text-muted font-13 font-numeric">({{ count($reserve_carts) }} کالا)</span>
                                        </div>
                                        @if($reserve_carts->isNotEmpty())
                                            <a wire:click="moveToAllMainCart" class="btn btn-sm btn-light rounded-8 text-primary font-weight-bold font-13" style="cursor: pointer;">
                                                <i class="mdi mdi-cart-arrow-right me-1"></i>افزودن همه به سبد اصلی
                                            </a>
                                        @endif
                                    </div>
                                    <div class="checkout-section-content-dd-k">
                                        <div class="cart-items-dd-k">
                                            @foreach($reserve_carts as $cart)
                                                @if($cart->product)
                                                    <div class="cart-item py-4 px-3 border-bottom d-flex gap-3 align-items-center">
                                                        <div class="item-thumbnail border rounded-12 p-1 bg-light">
                                                            <a href="#">
                                                                <img src="{{ url('images/products/big/'.$cart->product->image) }}" class="rounded-8" style="width: 90px; height: 90px; object-fit: cover;" alt="item">
                                                            </a>
                                                        </div>
                                                        <div class="item-info flex-grow-1">
                                                            <div class="item-title mb-2">
                                                                <h2 class="h6 font-weight-bold mb-0"><a href="#" class="text-dark text-decoration-none lh-base">{{ $cart->product->name }}</a></h2>
                                                            </div>
                                                            <div class="item-detail">
                                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                                                                    <div class="item-actions d-flex align-items-center gap-2">
                                                                        <button class="btn btn-sm btn-outline-danger rounded-8 px-2 font-12" wire:click="deleteCart({{ $cart->id }})">
                                                                            <i class="mdi mdi-trash-can-outline"></i> حذف
                                                                        </button>
                                                                        <button class="btn btn-sm btn-primary rounded-8 px-2 font-12" wire:click="moveToMainCart({{ $cart->id }})">
                                                                            <i class="mdi mdi-cart-arrow-up"></i> انتقال به سبد اصلی
                                                                        </button>
                                                                    </div>
                                                                    <div class="item-price font-numeric text-dark font-weight-black fs-5">
                                                                        {{ number_format($cart->product->final_price) }} <small class="text-muted font-11 font-weight-normal ms-1">تومان</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-12 w-res-sidebar sticky-sidebar ps-lg-3">
                                <div class="p-4 border rounded-20 bg-light-sidebar shadow-sm">
                                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                        <i class="mdi mdi-information-outline text-info fs-4"></i>
                                        <h2 class="h6 text-dark font-weight-black mb-0">لیست خرید بعدی چیست؟</h2>
                                    </div>
                                    <p class="text-secondary text-justify small lh-lg mb-0">
                                        شما می‌توانید محصولاتی که به سبد خرید خود افزوده اید و موقتاً قصد خرید آن‌ها را ندارید، در این بخش قرار داده تا در آینده راحت‌تر سفارش دهید.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
