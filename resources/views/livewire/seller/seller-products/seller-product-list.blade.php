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
            <th class="text-center">دلیل رد محصول</th>

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

                    <div>

                        @if($product->status === \App\Enums\ProductStatus::Approved->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i>
                                <span>تایید شده</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::Rejected->value)
                            <div class="modern-status-btn inactive">
                                <i class="ti-close mr-1"></i>
                                <span>رد شده</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::PendingReview->value)
                            <div class="modern-status-btn waiting">
                                <div class="status-pulse"></div>
                                <i class="ti-time mr-1"></i>
                                <span>ارسال شده برای بررسی</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::Draft->value)
                            <div class="modern-status-btn stop">
                                <i class="ti-control-pause mr-1"></i>
                                <span>درخواست اولیه</span>
                            </div>

                        @elseif($product->status === \App\Enums\ProductStatus::Archived->value)
                            <div class="modern-status-btn banned">
                                <i class="ti-na mr-1"></i>
                                <span>غیر فعال</span>
                            </div>
                        @endif

                    </div>
                </td>

                <td class="text-center align-middle">
                    @if($product->status === \App\Enums\ProductStatus::Rejected->value)

                        <button
                            class="btn btn-sm btn-outline-danger"
                            wire:click="showRejectReason({{ $product->id }})">

                            مشاهده دلیل
                        </button>

                    @else
                        <span class="text-muted">
                            --
                        </span>
                    @endif

                </td>

                <td class="text-center align-middle">

                    <a
                        href="{{ route('edit.seller.product',$product->id) }}"
                        class="btn btn-outline-info">

                        ویرایش

                    </a>

                </td>

                <td class="text-center align-middle">

                    <a
                        class="btn btn-outline-danger"
                        wire:click="$dispatch('deleteSellerProduct', {id: {{ $product->id }}})">
                        حذف
                    </a>

                </td>

                <td class="text-center align-middle">

                    {{ verta($product->created_at)->format('d F، Y') }}

                </td>

            </tr>

        @empty
            <tr>
                <td colspan="14" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        {{-- یک SVG ساده و شیک برای حالت جستجو --}}
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <small>
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </small>

                            <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                            <p class="text-muted">محصولی با عبارت <strong class="text-danger">"{{ $search }}"</strong>
                                در سیستم ثبت نشده است.</p>

                            @if($search)
                                <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="ti-eraser m-r-5"></i> پاکسازی جستجو
                                </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse

        </tbody>

    </table>

    <div class="modal fade @if($showRejectModal) show d-block @endif"
         tabindex="-1"
         style="background: rgba(0,0,0,0.5);">

        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">دلیل رد محصول</h5>
                    <button type="button" class="close" wire:click="$set('showRejectModal', false)">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if($rejectReason)
                        <p class="text-danger mb-0">
                            {{ $rejectReason }}
                        </p>
                    @else
                        <p class="text-muted">دلیلی ثبت نشده است</p>
                    @endif
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('showRejectModal', false)">
                        بستن
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div
        class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">

        {{ $products->appends(Request::except('page'))->links() }}

    </div>

</div>

@section('scripts')
    <script>
        Livewire.on('deleteSellerProduct', (event) => {

            Swal.fire({
                title: "آیا از حذف مطمئن هستید؟",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "بله",
                cancelButtonText: "خیر",
            }).then((result) => {

                if (result.isConfirmed) {

                    Livewire.dispatch('destroy_seller_product', {
                        id: event.id
                    });

                    Livewire.on('sellerProductDeleted', () => {
                        Swal.fire({
                            title: 'محصول حذف شد',
                            icon: 'success'
                        });
                    });

                    Livewire.on('sellerProductArchived', () => {
                        Swal.fire({
                            title: 'محصول حذف نشد',
                            text: 'به دلیل وجود سفارش، آرشیو شد',
                            icon: 'info'
                        });
                    });

                }
            });

        });
    </script>
@endsection
