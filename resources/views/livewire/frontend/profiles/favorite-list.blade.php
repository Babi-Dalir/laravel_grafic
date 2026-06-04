<div>

    <div class="row">

        @forelse($favorites as $favorite)

            <div class="col-lg-6 col-md-12">
                <div class="card-horizontal-product border-bottom rounded-0">

                    <div class="card-horizontal-product-thumb">
                        <a href="{{ route('single.product',$favorite->product->slug) }}">
                            <img src="{{ url('images/products/big/'.$favorite->product->image) }}" alt="">
                        </a>
                    </div>

                    <div class="card-horizontal-product-content">

                        <div class="card-horizontal-product-title">
                            <a href="{{ route('single.product',$favorite->product->slug) }}">
                                <h3>{{ $favorite->product->name }}</h3>
                            </a>
                        </div>

                        <div class="rating-stars">
                            <i class="mdi mdi-star active"></i>
                            <i class="mdi mdi-star active"></i>
                            <i class="mdi mdi-star active"></i>
                            <i class="mdi mdi-star active"></i>
                            <i class="mdi mdi-star"></i>
                        </div>

                        <div class="card-horizontal-product-price">
                            <span>
                                {{ number_format($favorite->product->main_price) }}
                                تومان
                            </span>
                        </div>

                        <div class="card-horizontal-product-buttons">

                            <a href="{{ route('single.product',$favorite->product->slug) }}"
                               class="btn">
                                مشاهده محصول
                            </a>

                            <button
                                class="remove-btn"
                                wire:click="deleteFavorite({{ $favorite->product->id }})">

                                <i class="mdi mdi-trash-can-outline"></i>

                            </button>

                        </div>

                    </div>

                </div>
            </div>

        @empty

            <div class="col-12">
                <div class="alert alert-warning">
                    هنوز محصولی به علاقه‌مندی‌ها اضافه نکرده‌اید.
                </div>
            </div>

        @endforelse

    </div>

    @if($favorites->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $favorites->links('vendor.pagination.profile-pagination.profile_favorites') }}
        </div>
    @endif

</div>
