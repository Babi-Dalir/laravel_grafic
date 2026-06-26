<div class="col-lg-9 col-md-12 col-sm-12 search-card-res">
    <div class="d-md-none">
        <button class="btn-filter-sidebar">
            جستجوی پیشرفته <i class="fad fa-sliders-h"></i>
        </button>
    </div>

    <div class="dt-sl dt-sn px-0 search-amazing-tab">
        <div class="ah-tab-wrapper dt-sl">
            <div class="ah-tab dt-sl">
                <a class="ah-tab-item" wire:click.prevent="allProducts" @if($sort === 'all') data-ah-tab-active="true" @endif href="#">همه محصولات دسته بندی {{ $currentCategory?->name }}</a>
                <a class="ah-tab-item" wire:click.prevent="newestProducts" @if($sort === 'newest') data-ah-tab-active="true" @endif href="#">جدید ترین</a>
                <a class="ah-tab-item" wire:click.prevent="moreSoldProducts" @if($sort === 'more_sold') data-ah-tab-active="true" @endif href="#">پرفروش ترین</a>
            </div>
        </div>

        <div class="ah-tab-content-wrapper dt-sl px-res-0">
            <div class="ah-tab-content dt-sl" data-ah-tab-active="true">
                <div class="row mb-3 mx-0 px-res-0">

                    @forelse($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 px-10 mb-1 px-res-0" wire:key="prod-idx-{{ $product->id }}">
                            <div class="product-card mb-2 mx-res-0">
                                @if($product->spacial_expiration != null && $product->spacial_expiration > now())
                                    <div class="promotion-badge">فروش ویژه</div>
                                @endif
                                <div class="product-head">
                                    <div class="rating-stars">
                                    </div>
                                    <div class="discount">
                                        @if($product->discount_percent > 0)
                                            <span>{{ $product->discount_percent }}%</span>
                                        @endif
                                    </div>
                                </div>
                                <a class="product-thumb" href="{{ route('single.product', $product->slug) }}">
                                    <img src="{{ url('images/products/big/'.$product->image) }}" alt="{{ $product->name }}">
                                </a>
                                <div class="product-card-body">
                                    <h5 class="product-title">
                                        <a href="{{ route('single.product', $product->slug) }}">{{ $product->name }}</a>
                                    </h5>
                                    <a class="product-meta" href="#">{{ $product->category?->name }}</a>

                                    @if($product->hasDiscount())
                                        <del class="text-danger small font-numeric">{{ number_format($product->main_price) }}</del>
                                        <span class="product-price font-numeric">{{ number_format($product->final_price) }} تومان</span>
                                    @else
                                        <span class="product-price font-numeric">{{ number_format($product->main_price) }} تومان</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            محصولی در این بخش یافت نشد.
                        </div>
                    @endforelse

                </div>

                {{-- رندر فرمت فایل‌های پجینیشن اختصاصی شما --}}
                @if($products->hasPages())
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center mt-3">
                            @if($sort === 'all')
                                {{ $products->links('vendor.pagination.products-pagination.all-products') }}
                            @elseif($sort === 'newest')
                                {{ $products->links('vendor.pagination.products-pagination.newest-products') }}
                            @elseif($sort === 'more_sold')
                                {{ $products->links('vendor.pagination.products-pagination.more-sold-products') }}
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
