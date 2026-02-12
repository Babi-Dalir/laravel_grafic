<div class="dt-sn mb-5 dt-sl">
    <div class="row">
        <!-- Product Gallery-->
        <div class="col-lg-4 col-md-6 ps-relative">
            <!-- Product Options-->
            <ul class="gallery-options">
                <div>
                    @if(session()->has('message'))
                        <div class="text-danger">
                            <div>{{session('message')}}</div>
                        </div>
                    @endif

                </div>
                @php
                    if (auth()->user()){
                        $favorite = \App\Models\Favorite::query()
                    ->where('user_id',auth()->user()->id)
                    ->where('product_id',$product->id)
                    ->exists();
                    }else{
                        $favorite = null;
                    }
                @endphp
                <li wire:click="AddFavorite({{$product->id}})">
                    <button class="add-favorites"><i class="mdi mdi-heart @if($favorite) text-danger @endif"></i>
                    </button>
                    <span class="tooltip-option">افزودن به علاقمندی</span>
                </li>
            </ul>
            @if($product->spacial_expiration !=null && $product->spacial_expiration > now())
                <div class="product-timeout position-relative pt-5 mb-3">
                    <div class="promotion-badge">
                        فروش ویژه
                    </div>
                    <div class="countdown-timer" countdown data-date="{{$product->spacial_expiration}}">
                        <span data-days>0</span>:
                        <span data-hours>0</span>:
                        <span data-minutes>0</span>:
                        <span data-seconds>0</span>
                    </div>
                </div>
            @endif
            <div class="product-gallery" wire:ignore>
                <div class="product-carousel owl-carousel" data-slider-id="1">
                    @foreach($product->galleries as $gallery)
                        <div class="item">
                            <a class="gallery-item" href="{{url('images/products/big/'.$gallery->image)}}"
                               data-fancybox="gallery1">
                                <img src="{{url('images/products/big/'.$gallery->image)}}" alt="Product">
                            </a>
                        </div>
                    @endforeach

                </div>
                <div class="d-flex justify-content-center flex-wrap">
                    <ul class="product-thumbnails owl-thumbs ml-2" data-slider-id="1">
                        @foreach($product->galleries as $gallery)
                            <li class="owl-thumb-item active">
                                <a href="">
                                    <img src="{{url('images/products/big/'.$gallery->image)}}" alt="Product">
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- Product Info -->
        <div class="col-lg-8 col-md-6 py-2">
            <div class="product-info dt-sl">
                <div class="product-title dt-sl mb-3">
                    <h1>{{$product->name}}</h1>
                    <h3>{{$product->e_name}}</h3>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="product-variant dt-sl">
                            <div
                                class="section-title text-sm-title title-wide no-after-title-wide mb-0">
                                <h2>انتخاب رنگ:</h2>
                            </div>
                            <ul class="product-variants float-right ml-3">
                                @foreach($product->colors as $color)
                                    @if($product->productPrices()->where('color_id',$color->id)->where('count','>',0)->first())
                                        <li class="ui-variant" wire:click="changeColorProduct({{$color->id}})">
                                            <label class="ui-variant ui-variant--color">
                                                        <span class="ui-variant-shape"
                                                              style="background-color: {{$color->code}}"></span>
                                                <input type="radio" value="1" name="color"
                                                       class="variant-selector"
                                                       @if($product->productPrices()->where('price',$product->price)->first()->color_id == $color->id) checked @endif>
                                                <span class="ui-variant--check">{{$color->name}}</span>
                                            </label>
                                        </li>
                                    @endif
                                @endforeach

                            </ul>
                        </div>
                        <div class="product-params dt-sl">
                            <ul data-title="ویژگی‌های محصول">
                                @foreach($product->propertyGroups as $propertyGroup)
                                    <li>
                                        <span>{{$propertyGroup->name}}: </span>
                                        <span> {{$propertyGroup->properties->where('product_id',$product->id)->pluck('name')->implode(',')}} </span>
                                    </li>
                                @endforeach

                            </ul>
                            <div class="sum-more">
                                                <span class="show-more btn-link-border">
                                                    + موارد بیشتر
                                                </span>
                                <span class="show-less btn-link-border">
                                                    - بستن
                                                </span>
                            </div>
                        </div>
                        <div
                            class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl">
                            <h2>کد محصول:225566</h2>
                        </div>
                    </div>
                    <div class="col-lg-6" wire:ignore.self>

                        <div class="product-summary" wire:ignore.self>
                            <nav id="stack-menu" wire:ignore.self>
                                <ul wire:ignore.self>

                                    <li wire:ignore.self>
                                        <a>
                                            <i class="far fa-shield-check"></i>
                                            {{$product_price->guaranty->name}}
                                        </a>
                                    </li>
                                    <li wire:ignore>
                                        <a href="#">
                                            <i class="far fa-box-check product-delivery-warehouse"></i>
                                            موجود در انبار بابی شاپ
                                        </a>

                                    </li>
                                </ul>
                                <div class="product-seller-row product-seller-row--price" wire:ignore.self>
                                    <div class="product-seller-price-info" wire:ignore.self>
                                        <div class="product-seller-price-prev" wire:ignore.self>
                                            {{number_format(($product_price->main_price))}}
                                        </div>
                                        @if($product_price->discount >0)
                                            <div class="product-seller-price-off" wire:ignore.self>
                                                {{$product_price->discount}}٪
                                            </div>
                                        @endif
                                    </div>
                                    <div class="product-seller-price-real" wire:ignore.self>
                                        <div
                                            class="product-seller-price-raw">{{number_format($product_price->price)}}</div>
                                        تومان
                                    </div>
                                    <div
                                        class="product-additional-item product-additional-item--no-icon"
                                        wire:ignore.self>
                                        <span>{{number_format($product_price->main_price - $product_price->price)}}</span>&nbsp;
                                        تومان تخفیف شما از خرید این محصول کسر گردیده است.
                                    </div>
                                </div>
                                <div class="product-seller-row product-seller-row--add-to-cart"
                                     wire:click="addToCart({{$product_price->color_id}},{{$product_price->guaranty_id}})">
                                    <a href="#" class="btn-add-to-cart btn-add-to-cart--full-width" wire:ignore>
                                        <span class="btn-add-to-cart-txt">افزودن به سبد خرید</span>
                                    </a>

                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
