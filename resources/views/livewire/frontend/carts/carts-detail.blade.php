<div>
    @if($carts->isEmpty() && $reserve_carts->isEmpty())
        <div class="dt sl dt-sn dt-sn--box border pt-3 pb-5">
            <div class="cart-page cart-empty">
                <div class="circle-box-icon">
                    <i class="mdi mdi-cart-remove"></i>
                </div>
                <p class="cart-empty-title">سبد خرید شما خالیست!</p>
            </div>
        </div>
    @else
        <div class="row mx-0">
            <div class="col-xl-9 col-lg-8 col-md-12 col-sm-12 mb-2">
                <nav class="tab-cart-page">
                    <div class="nav nav-tabs border-bottom" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link d-inline-flex w-auto active" id="nav-home-tab"
                           data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home"
                           aria-selected="true">سبد خرید<span class="count-cart">{{ count($carts) }}</span></a>
                        <a class="nav-item nav-link d-inline-flex w-auto" id="nav-profile-tab" data-toggle="tab"
                           href="#nav-profile" role="tab" aria-controls="nav-profile"
                           aria-selected="false">لیست خرید بعدی<span class="count-cart">{{ count($reserve_carts) }}</span></a>
                    </div>
                </nav>
            </div>

            <div class="col-12">
                <div class="tab-content" id="nav-tabContent">
                    {{-- تب سبد خرید اصلی --}}
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="row">
                            <div class="col-xl-9 col-lg-8 col-12 px-0">
                                <div class="table-responsive checkout-content dt-sl">
                                    <div class="checkout-header checkout-header--express">
                                        <span class="checkout-header-title">ارسال عادی</span>
                                        <span class="checkout-header-extra-info">({{ count($carts) }} کالا)</span>
                                        <span wire:loading class="text text-info mr-3">درحال بروزرسانی...</span>
                                    </div>
                                    <div class="checkout-section-content-dd-k">
                                        <div class="cart-items-dd-k">
                                            @foreach($carts as $cart)
                                                @if($cart->product)
                                                    <div class="cart-item py-4 px-3">
                                                        <div class="item-thumbnail">
                                                            <a href="#">
                                                                <img src="{{ url('images/products/big/'.$cart->product->image) }}" alt="item">
                                                            </a>
                                                        </div>
                                                        <div class="item-info flex-grow-1">
                                                            <div class="item-title">
                                                                <h2><a href="#">{{ $cart->product->name }}</a></h2>
                                                            </div>
                                                            <div class="item-detail">
                                                                <ul>
                                                                    <li>
                                                                        <i class="far fa-store-alt text-muted"></i>
                                                                        <span>نام فروشنده</span>
                                                                    </li>
                                                                    <li>
                                                                        <i class="far fa-download text-primary"></i>
                                                                        <span>دانلود آنی پس از پرداخت</span>
                                                                    </li>
                                                                </ul>
                                                                <div class="item-quantity--item-price">
                                                                    <div class="item-actions d-flex align-items-center gap-2 mt-2">
                                                                        <button wire:click="deleteCart({{ $cart->id }})" class="action-btn danger">
                                                                            <i class="far fa-trash-alt"></i> <span class="text">حذف</span>
                                                                        </button>
                                                                        <button wire:click="moveToReserveCart({{ $cart->id }})" class="action-btn primary">
                                                                            <i class="far fa-clock"></i> <span class="text">بعداً می‌خرم</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="item-price">
                                                                        {{ number_format($cart->product->final_price) }} <span class="text-sm mr-1">تومان</span>
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

                            {{-- سدیدبار فاکتور محاسبات مالی فاکتور --}}
                            <div class="col-xl-3 col-lg-4 col-12 w-res-sidebar sticky-sidebar">
                                <div class="dt-sn dt-sn--box border mb-2">
                                    <ul class="checkout-summary-summary">
                                        <li>
                                            <span>مبلغ کل کالاها</span><span>{{ number_format($total_price) }} تومان</span>
                                        </li>
                                        @if($discount_price > 0)
                                            <li class="checkout-summary-discount">
                                                <span>سود شما از تخفیف کالاها</span><span>{{ number_format($discount_price) }} تومان</span>
                                            </li>
                                        @endif
                                        @if($discount_code_price > 0)
                                            <li class="checkout-summary-discount">
                                                <span class="text-info">کد تخفیف اعمال شده</span><span>{{ number_format($discount_code_price) }} - تومان</span>
                                            </li>
                                        @endif
                                        @if($gift_cart_price > 0)
                                            <li class="checkout-summary-discount">
                                                <span class="text-info">کارت هدیه اعمال شده</span><span>{{ number_format($gift_cart_price) }} - تومان</span>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="checkout-summary-devider"><div></div></div>
                                    <div class="checkout-summary-content">
                                        <div class="checkout-summary-price-title">مبلغ قابل پرداخت:</div>
                                        <div class="checkout-summary-price-value">
                                            <span class="checkout-summary-price-value-amount">{{ number_format($final_price) }}</span> تومان
                                        </div>
                                        @if($carts->isNotEmpty())
                                            <button wire:click="submitPayment" class="btn-primary-cm btn-with-icon w-100 text-center pr-0 mt-2">
                                                <i class="mdi mdi-arrow-left"></i> پرداخت و ثبت نهایی سفارش
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- بخش تخفیف‌ها و کارت هدیه --}}
                        <div class="row mt-4 mx-0">
                            <div class="col-md-6 col-12 mb-3">
                                <div class="dt-sn dt-sn--box pt-3 pb-3 px-3 h-100">
                                    <div class="section-title text-sm-title title-wide no-after-title-wide mb-0">
                                        <h2>استفاده از کارت هدیه</h2>
                                    </div>
                                    <p class="mb-3 text-muted">با ثبت کد کارت هدیه، مبلغ آن از فاکتور کسر می‌شود.</p>
                                    <div class="form-ui">
                                        <form wire:submit.prevent="giftCartCode">
                                            <div class="row align-items-start">
                                                <div class="col-lg-8 col-md-7 col-12 mb-2">
                                                    <input type="text" class="input-ui pr-2" wire:model="gift_cart_code" placeholder="مثلا GIFT-XM42">
                                                </div>
                                                <div class="col-lg-4 col-md-5 col-12 mb-2">
                                                    <button type="submit" class="btn btn-primary w-100">ثبت کد</button>
                                                </div>
                                            </div>
                                            @if(session()->has('success_gift_cart'))
                                                <div class="alert alert-success mt-2">{{ session('success_gift_cart') }}</div>
                                            @endif
                                            @if(session()->has('warning_gift_cart'))
                                                <div class="alert alert-danger mt-2">{{ session('warning_gift_cart') }}</div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-12 mb-3">
                                <div class="dt-sn dt-sn--box pt-3 pb-3 px-3 h-100">
                                    <div class="section-title text-sm-title title-wide no-after-title-wide mb-0">
                                        <h2>استفاده از کد تخفیف</h2>
                                    </div>
                                    <p class="mb-3 text-muted">کد تخفیف خود را در کادر زیر وارد نمایید.</p>
                                    <div class="form-ui">
                                        <form wire:submit.prevent="discountCode">
                                            <div class="row align-items-start">
                                                <div class="col-lg-8 col-md-7 col-12 mb-2">
                                                    <input type="text" class="input-ui pr-2" wire:model="discount_code" placeholder="مثلا OFF-1405">
                                                </div>
                                                <div class="col-lg-4 col-md-5 col-12 mb-2">
                                                    <button type="submit" class="btn btn-primary w-100">اعمال تخفیف</button>
                                                </div>
                                            </div>
                                            @if(session()->has('success_discount'))
                                                <div class="alert alert-success mt-2">{{ session('success_discount') }}</div>
                                            @endif
                                            @if(session()->has('warning_discount'))
                                                <div class="alert alert-danger mt-2">{{ session('warning_discount') }}</div>
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
                                <div class="table-responsive checkout-content dt-sl">
                                    <div class="checkout-header checkout-header--express">
                                        <span class="checkout-header-title">لیست خرید بعدی</span>
                                        <span class="checkout-header-extra-info">({{ count($reserve_carts) }} کالا)</span>
                                        @if($reserve_carts->isNotEmpty())
                                            <a wire:click="moveToAllMainCart" class="checkout-add-all-to-cart" style="cursor: pointer;">
                                                افزودن همه به سبد خرید اصلی
                                            </a>
                                        @endif
                                    </div>
                                    <div class="checkout-section-content-dd-k">
                                        <div class="cart-items-dd-k">
                                            @foreach($reserve_carts as $cart)
                                                @if($cart->product)
                                                    <div class="cart-item py-4 px-3">
                                                        <div class="item-thumbnail">
                                                            <a href="#">
                                                                <img src="{{ url('images/products/big/'.$cart->product->image) }}" alt="item">
                                                            </a>
                                                        </div>
                                                        <div class="item-info flex-grow-1">
                                                            <div class="item-title">
                                                                <h2><a href="#">{{ $cart->product->name }}</a></h2>
                                                            </div>
                                                            <div class="item-detail">
                                                                <div class="item-quantity--item-price">
                                                                    <div class="item-actions d-flex align-items-center gap-2">
                                                                        <button class="action-chip danger mr-2" wire:click="deleteCart({{ $cart->id }})">
                                                                            <i class="far fa-trash-alt"></i> <span>حذف</span>
                                                                        </button>
                                                                        <button class="action-chip primary" wire:click="moveToMainCart({{ $cart->id }})">
                                                                            <i class="far fa-shopping-cart"></i> <span>انتقال به سبد اصلی</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="item-price">
                                                                        {{ number_format($cart->product->final_price) }} <span class="text-sm mr-1">تومان</span>
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
                            <div class="col-xl-3 col-lg-4 col-12 w-res-sidebar sticky-sidebar">
                                <div class="dt-sn dt-sn--box border">
                                    <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide mb-2">
                                        <h2 class="text-dark">لیست خرید بعدی چیست؟</h2>
                                    </div>
                                    <p class="text-secondary text-justify small">
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
