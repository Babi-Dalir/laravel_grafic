<div class="table overflow-auto">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو</label>
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
            <th class="text-center">ویژگی های محصول</th>
            <th class="text-center">گالری</th>
            {{-- 🟢 هدر جدید برای مدیریت و آپلود فایل‌ها --}}
            <th class="text-center">فایل‌های محصول</th>
            <th class="text-center">وضعیت</th>
            <th class="text-center">دلیل رد محصول</th>
            <th class="text-center">تکمیل محصول</th>
            <th class="text-center">ویرایش</th>
            <th class="text-center">حذف</th>
            <th class="text-center">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($products as $index => $product)
            @php
                // واکشی آرایه چک‌لیست وضعیت بررسی محصول برای تک‌تک بخش‌ها به صورت مجزا
                $checklist = $product->reviewChecklist();

                $hasProperties = $checklist['properties'] ?? false;
                $hasGallery    = $checklist['gallery'] ?? false;
                $hasFiles      = $checklist['files'] ?? false;

                $percent = $product->completion_percent;
            @endphp

            <tr wire:key="product-row-{{ $product->id }}">
                <td class="text-center align-middle">{{ $products->firstItem() + $index }}</td>
                <td class="text-center align-middle">
                    <img src="{{ url('images/products/small/'.$product->image) }}" width="60" class="rounded" alt="">
                </td>
                <td class="text-center align-middle">{{ $product->name }}</td>
                <td class="text-center align-middle">{{ $product->category?->name ?? '--' }}</td>
                <td class="text-center align-middle">{{ number_format($product->main_price) }} تومان</td>
                <td class="text-center align-middle">
                    @if($product->discount_percent)
                        <span class="badge badge-danger">{{ $product->discount_percent }} %</span>
                    @else
                        --
                    @endif
                </td>
                <td class="text-center align-middle">{{ number_format($product->final_price) }} تومان</td>

                <td class="text-center align-middle">{{ $product->total_download_count ?? 0 }}</td>

                {{-- 🎯 ستون ویژگی‌ها (بررسی کاملاً اختصاصی) --}}
                <td class="text-center align-middle">
                    <a class="btn {{ !$hasProperties ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-secondary' }}"
                       href="{{ route('create.seller.product.properties', $product) }}">
                        @if(!$hasProperties)
                            <i class="ti-alert mr-1"></i> ویژگی‌ها (ناقص)
                        @else
                            ویژگی‌ها
                        @endif
                    </a>
                </td>

                {{-- 🎯 ستون گالری عکس (بررسی کاملاً اختصاصی) --}}
                <td class="text-center align-middle">
                    <a class="btn {{ !$hasGallery ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-success' }}"
                       href="{{ route('add.seller.product.gallery', $product->id) }}">
                        <i class="ti-cloud-up mr-1"></i>
                        @if(!$hasGallery)
                            آپلود عکس (ناقص)
                        @else
                            گالری
                        @endif
                    </a>
                </td>

                {{-- 🎯 ستون آپلود فایل دیجیتال (بررسی کاملاً اختصاصی) --}}
                <td class="text-center align-middle">
                    <a class="btn {{ !$hasFiles ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-primary' }}"
                       href="{{ route('seller.product.file.list', $product) }}">
                        <i class="ti-cloud-up mr-1"></i>
                        @if(!$hasFiles)
                            آپلود فایل (ناقص)
                        @else
                            مدیریت فایل‌ها
                        @endif
                    </a>
                </td>

                <td class="text-center align-middle">
                    <div>
                        @if($product->status === \App\Enums\ProductStatus::Approved->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i><span>تایید شده</span>
                            </div>
                        @elseif($product->status === \App\Enums\ProductStatus::Rejected->value)
                            <div class="modern-status-btn inactive">
                                <i class="ti-close mr-1"></i><span>رد شده</span>
                            </div>
                        @elseif($product->status === \App\Enums\ProductStatus::PendingReview->value)
                            <div class="modern-status-btn waiting">
                                <div class="status-pulse"></div>
                                <i class="ti-time mr-1"></i><span>در انتظار بررسی</span>
                            </div>
                        @elseif($product->status === \App\Enums\ProductStatus::Draft->value)
                            <div class="modern-status-btn stop">
                                <i class="ti-control-pause mr-1"></i><span>درخواست اولیه</span>
                            </div>
                        @elseif($product->status === \App\Enums\ProductStatus::Archived->value)
                            <div class="modern-status-btn banned">
                                <i class="ti-na mr-1"></i><span>غیر فعال</span>
                            </div>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle">
                    @if($product->status === \App\Enums\ProductStatus::Rejected->value)
                        <button class="btn btn-sm btn-outline-danger" wire:click="showRejectReason({{ $product->id }})">
                            <i class="ti-eye mr-1"></i> مشاهده دلیل
                        </button>
                    @else
                        <span class="text-muted">--</span>
                    @endif
                </td>

                <td class="text-center align-middle">
                    <div class="progress">
                        <div
                            class="progress-bar {{ $percent == 100 ? 'bg-success' : ($percent >= 70 ? 'bg-info' : ($percent >= 40 ? 'bg-warning' : 'bg-danger')) }}"
                            style="width: {{ $percent }}%">
                            {{ $percent }}%
                        </div>
                    </div>
                </td>

                <td class="text-center align-middle">
                    <a href="{{ route('edit.seller.product', $product->id) }}" class="btn btn-outline-info">ویرایش</a>
                </td>
                <td class="text-center align-middle">
                    <button class="btn btn-outline-danger"
                            wire:click="$dispatch('deleteSellerProduct', {id: {{ $product->id }}})">حذف
                    </button>
                </td>
                <td class="text-center align-middle">{{ verta($product->created_at)->format('d F، Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="17" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                        <p class="text-muted">محصولی با عبارت <strong class="text-danger">"{{ $search }}"</strong> یافت نشد.</p>
                        @if($search)
                            <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">پاکسازی جستجو</button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- مدال نمایش علت رد شدن با مدیریت استایل نمایشی لایووایر --}}
    <div class="modal fade @if($showRejectModal) show d-block @endif" tabindex="-1" role="dialog"
         style="background: rgba(0,0,0,0.5); display: @if($showRejectModal) block @else none @endif;">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title font-weight-bold text-dark">
                        <i class="ti-alert text-danger mr-2"></i> دلیل عدم تایید محصول
                    </h5>
                    <button type="button" class="close" wire:click="$set('showRejectModal', false)" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div class="p-3 bg-light border-right border-danger"
                         style="border-right-width: 4px; border-radius: 4px;">
                        <p class="text-danger font-weight-bold mb-0" style="line-height: 1.8; font-size: 14px;">
                            {{ $rejectReason }}
                        </p>
                    </div>
                </div>
                <div class="modal-footer bg-light"
                     style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button class="btn btn-secondary px-4" wire:click="$set('showRejectModal', false)">بستن پنجره
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
        {{ $products->appends(Request::except('page'))->links() }}
    </div>
</div>

@section('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('deleteSellerProduct', (event) => {
                Swal.fire({
                    title: "آیا از حذف مطمئن هستید؟",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "بله",
                    cancelButtonText: "خیر",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('destroy_seller_product', {id: event.id});
                    }
                });
            });

            Livewire.on('sellerProductDeleted', () => {
                Swal.fire({title: 'محصول حذف شد', icon: 'success'});
            });

            Livewire.on('sellerProductArchived', () => {
                Swal.fire({
                    title: 'محصول غیر فعال شد',
                    text: 'به دلیل وجود سفارشات متصل، محصول به جای حذف فیزیکی آرشیو گردید.',
                    icon: 'info'
                });
            });
        });
    </script>
@endsection
