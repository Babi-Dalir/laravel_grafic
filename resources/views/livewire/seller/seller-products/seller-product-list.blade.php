<div class="table overflow-auto">

    <div class="form-group row">

        <label class="col-sm-2 col-form-label">
            جستجو
        </label>

        <div class="col-sm-10">

            <input
                type="text"
                class="form-control"
                wire:model.live.debounce.500ms="search"
                placeholder="نام محصول را وارد کنید">

        </div>

    </div>

    <table class="table table-striped table-hover">

        <thead>

        <tr>

            <th class="text-center">ردیف</th>

            <th class="text-center">تصویر</th>

            <th class="text-center">نام محصول</th>

            <th class="text-center">دسته بندی</th>

            <th class="text-center">قیمت</th>

            <th class="text-center">تخفیف</th>

            <th class="text-center">قیمت نهایی</th>

            <th class="text-center">دانلود</th>

            <th class="text-center">وضعیت</th>

            <th class="text-center">ویرایش</th>

            <th class="text-center">حذف</th>

            <th class="text-center">تاریخ ایجاد</th>

        </tr>

        </thead>

        <tbody>

        @forelse($products as $index => $product)

            <tr>

                <td class="text-center align-middle">
                    {{ $products->firstItem() + $index }}
                </td>

                <td class="text-center align-middle">

                    <img
                        src="{{ url('images/products/small/'.$product->image) }}"
                        width="60"
                        class="rounded">

                </td>

                <td class="text-center align-middle">

                    {{ $product->name }}

                </td>

                <td class="text-center align-middle">

                    {{ $product->category?->name }}

                </td>

                <td class="text-center align-middle">

                    {{ number_format($product->main_price) }}

                    تومان

                </td>

                <td class="text-center align-middle">

                    @if($product->discount_percent)

                        <span class="badge badge-danger">

                            {{ $product->discount_percent }} %

                        </span>

                    @else

                        --

                    @endif

                </td>

                <td class="text-center align-middle">

                    {{ number_format($product->final_price) }}

                    تومان

                </td>

                <td class="text-center align-middle">

                    {{ $product->downloads()->sum('download_count') }}

                </td>

                <td class="text-center align-middle">

                    <div class="status-interactive-wrapper">

                        @if($product->status === \App\Enums\ProductStatus::Active->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i>
                                <span>تایید شده</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::InActive->value)
                            <div class="modern-status-btn inactive">
                                <i class="ti-close mr-1"></i>
                                <span>رد شده</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::Waiting->value)
                            <div class="modern-status-btn waiting">
                                <div class="status-pulse"></div>
                                <i class="ti-time mr-1"></i>
                                <span>در حال بررسی</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::Draft->value)
                            <div class="modern-status-btn stop">
                                <i class="ti-control-pause mr-1"></i>
                                <span>نیاز به ویرایش</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::Rejected->value)
                            <div class="modern-status-btn banned">
                                <i class="ti-na mr-1"></i>
                                <span>غیر مجاز</span>
                            </div>
                        @endif

                    </div>

                </td>

                <td class="text-center align-middle">

                    <a
                        href="{{ route('products.edit',$product->id) }}"
                        class="btn btn-outline-info btn-sm">

                        ویرایش

                    </a>

                </td>

                <td class="text-center align-middle">

                    <button
                        class="btn btn-outline-danger btn-sm"
                        wire:click="$dispatch('deleteProduct',{id:{{ $product->id }}})">

                        حذف

                    </button>

                </td>

                <td class="text-center align-middle">

                    {{ verta($product->created_at)->format('d F، Y') }}

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="12" class="text-center py-5">

                    محصولی یافت نشد

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div
        class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">

        {{ $products->appends(Request::except('page'))->links() }}

    </div>

</div>
