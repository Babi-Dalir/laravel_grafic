<div class="nav-item cart--wrapper">
    <a class="nav-link" href="#">
        <span class="label-dropdown">سبد خرید</span>
        <i class="mdi mdi-cart-outline"></i>
        <span class="count">{{count($carts)}}</span>
    </a>
    <div class="header-cart-info">
        <div class="header-cart-info-header">
            <div class="header-cart-info-count">
                {{count($carts)}} کالا
            </div>
            <a href="{{route('user.cart')}}" class="header-cart-info-link">
                <span>مشاهده سبد خرید</span>
            </a>
        </div>
        <ul class="header-basket-list do-nice-scroll">
            @foreach($carts as $cart)
                <li class="cart-item border-bottom py-2">
                    <div class="header-basket-list-item d-flex align-items-center">

                        {{-- تصویر محصول --}}
                        <div class="header-basket-list-item-image ml-2">
                            <img
                                src="{{ url('images/products/small/'.$cart->product->image) }}"
                                alt="{{ $cart->product->name }}"
                                style="width:70px;height:70px;object-fit:cover;border-radius:10px;"
                            >
                        </div>

                        {{-- اطلاعات محصول --}}
                        <div class="header-basket-list-item-content flex-grow-1">

                            <div class="d-flex justify-content-between align-items-start">

                                <div>
                                    <h6 class="mb-1 font-weight-bold">
                                        {{ $cart->product->name }}
                                    </h6>

                                    @if($cart->product->discount_percent > 0)
                                        <small class="text-danger">
                                            {{ $cart->product->discount_percent }}٪ تخفیف
                                        </small>
                                    @endif
                                </div>

                                {{-- حذف --}}
                                <button
                                    wire:click="deleteCart({{ $cart->id }})"
                                    class="btn btn-sm text-danger p-0"
                                    title="حذف از سبد خرید"
                                >
                                    <i class="mdi mdi-delete-outline" style="font-size:22px"></i>
                                </button>

                            </div>

                            {{-- قیمت --}}
                            <div class="mt-2">
                                @if($cart->product->main_price > $cart->product->final_price)
                                    <small class="text-muted">
                                        <del>
                                            {{ number_format($cart->product->main_price) }}
                                        </del>
                                    </small>
                                @endif

                                <div class="font-weight-bold text-success">
                                    {{ number_format($cart->product->final_price) }}
                                    تومان
                                </div>
                            </div>

                        </div>

                    </div>
                </li>
            @endforeach
        </ul>
        @if(count($carts) > 0)

            <div class="header-cart-info-footer">

                <div class="header-cart-info-total text-center mb-3">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted text-nowrap">
                            مبلغ قابل پرداخت :
                        </span>

                        <span class="text-success font-weight-bold text-nowrap" style="font-size:22px;">
                            {{ number_format($final_price) }} تومان
                        </span>
                    </div>

                    @if($discount_price > 0)
                        <small class="text-nowrap" style="font-size:15px;font-weight:bold;color:red;">
                            {{ number_format($discount_price) }}
                            تومان تخفیف
                        </small>
                    @endif

                </div>
            </div>
        @else
            <div class="header-cart-info-footer">
                <div class="header-cart-info-total">
                    <span class="header-cart-info-total-text">سبد خرید شما خالی است!</span>
                </div>
            </div>
        @endif

    </div>
</div>
