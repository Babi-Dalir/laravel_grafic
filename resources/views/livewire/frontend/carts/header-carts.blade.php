<div class="nav-item cart--wrapper">
    <a class="nav-link" href="{{ route('user.cart') }}">
        <span class="label-dropdown">سبد خرید</span>
        <i class="mdi mdi-cart-outline"></i>
        {{-- شمارنده پویا و سریع --}}
        <span class="count">{{ $carts->count() }}</span>
    </a>

    <div class="header-cart-info">
        <div class="header-cart-info-header">
            <div class="header-cart-info-count">
                {{ $carts->count() }} کالا
            </div>
            <a href="{{ route('user.cart') }}" class="header-cart-info-link">
                <span>مشاهده و ویرایش سبد</span>
            </a>
        </div>

        <ul class="header-basket-list do-nice-scroll">
            @foreach($carts as $cart)
                {{-- 🟢 افزودن wire:key الزامی است تا لایووایر موقع حذف ردیف‌ها را قاطی نکند --}}
                <li class="cart-item border-bottom py-2" wire:key="header-cart-item-{{ $cart->id }}">
                    <div class="header-basket-list-item d-flex align-items-center">

                        {{-- تصویر محصول --}}
                        <div class="header-basket-list-item-image ml-2">
                            <img
                                src="{{ $cart->product->image ? asset('images/products/small/'.$cart->product->image) : asset('images/products/default.png') }}"
                                alt="{{ $cart->product->name }}"
                                style="width:70px;height:70px;object-fit:cover;border-radius:10px;"
                            >
                        </div>

                        {{-- اطلاعات محصول --}}
                        <div class="header-basket-list-item-content flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 font-weight-bold text-dark" style="font-size: 14px; line-height: 1.6;">
                                        {{ $cart->product->name }}
                                    </h6>

                                    @if($cart->product->discount_percent > 0)
                                        <span class="badge badge-danger font-numeric">
                                            {{ $cart->product->discount_percent }}٪ تخفیف
                                        </span>
                                    @endif
                                </div>

                                {{-- دکمه حذف هوشمند به همراه لودینگ آیکون تک‌محصوله --}}
                                <div class="delete-action-container">
                                    <button
                                        wire:click="deleteCart({{ $cart->id }})"
                                        wire:loading.delay.class="opacity-50"
                                        class="btn btn-sm text-danger p-0 border-0 bg-transparent"
                                        title="حذف از سبد خرید"
                                    >
                                        <i class="mdi mdi-delete-outline" style="font-size:22px"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- لایه نمایش قیمت کالا --}}
                            <div class="mt-2 text-right">
                                @if($cart->product->main_price > $cart->product->final_price)
                                    <small class="text-muted text-muted-del d-block mb-1">
                                        <del class="font-numeric">{{ number_format($cart->product->main_price) }}</del>
                                    </small>
                                @endif

                                <div class="font-weight-bold text-success font-numeric">
                                    {{ number_format($cart->product->final_price) }}
                                    <span style="font-size: 11px;" class="mr-1 text-muted">تومان</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </li>
            @endforeach
        </ul>

        @if($carts->isNotEmpty())
            <div class="header-cart-info">
                <div class="header-cart-info-total text-center mb-2 p-3 bg-light rounded">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted text-nowrap">مبلغ قابل پرداخت:</span>
                        <span class="text-success font-weight-bold text-nowrap font-numeric" style="font-size:20px;">
                            {{ number_format($final_price) }} <small style="font-size: 12px" class="text-muted">تومان</small>
                        </span>
                    </div>

                    @if($discount_price > 0)
                        <div class="text-right text-danger font-weight-bold font-numeric" style="font-size:13px;">
                            <i class="mdi mdi-tag-heart mr-1"></i> سود شما از این خرید: {{ number_format($discount_price) }} تومان
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="header-cart-info-footer py-4 text-center">
                <div class="header-cart-info-total justify-content-center">
                    <i class="mdi mdi-cart-remove text-muted mb-2 d-block" style="font-size: 32px;"></i>
                    <span class="header-cart-info-total-text font-weight-bold text-muted">سبد خرید شما در حال حاضر خالی است.</span>
                </div>
            </div>
        @endif

    </div>
</div>
