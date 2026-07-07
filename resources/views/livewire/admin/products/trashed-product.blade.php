<div class="table-responsive overflow-auto" tabindex="8">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="form-group row align-items-center mb-4">
        <label class="col-md-2 col-sm-12 col-form-label font-weight-bold text-secondary">جستجو (نام محصول):</label>
        <div class="col-md-7 col-sm-12 d-flex align-items-center mb-2 mb-md-0">
            <div class="position-relative w-100 d-flex align-items-center">
                <input type="text" class="form-control text-left pr-3 pl-5" dir="rtl"
                       wire:model.live.debounce.500ms="search" placeholder="عبارت مورد نظر را تایپ کنید...">
                <div wire:loading class="spinner-border spinner-border-sm text-primary position-absolute" style="left: 15px;"></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-12 text-left">
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-between p-2 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-box ml-3">
                        <i class="ti-list text-primary"></i>
                    </div>
                    <div class="text-content text-right">
                        <span class="title d-block font-weight-bold text-dark">بازگشت به لیست</span>
                        <span class="count text-muted small">مشاهده همه محصولات</span>
                    </div>
                </div>
                <i class="ti-angle-left text-muted"></i>
            </a>
        </div>
    </div>

    <table class="table table-striped table-hover m-0 align-middle text-center bg-white border">
        <thead class="thead-light">
        <tr>
            <th class="align-middle text-primary">ردیف</th>
            <th class="align-middle text-primary">عکس</th>
            <th class="align-middle text-primary">عنوان محصول</th>
            <th class="align-middle text-primary">نام فروشنده</th>
            <th class="align-middle text-primary">دسته‌بندی</th>
            <th class="align-middle text-primary">بازگردانی</th>
            <th class="align-middle text-primary">حذف دائمی</th>
            <th class="align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($products as $index => $product)
            <tr>
                <td class="align-middle font-weight-bold">{{ $products->firstItem() + $index }}</td>
                <td class="align-middle">
                    <figure class="avatar mb-0 m-auto">
                        <img src="{{ $product->image ? url('images/products/small/'.$product->image) : url('images/products/default.png') }}"
                             width="45" height="45" class="rounded-circle shadow-sm" style="object-fit: cover;" alt="product image">
                    </figure>
                </td>
                <td class="align-middle font-weight-bold text-center pr-3 text-dark">{{ $product->name }}</td>

                <td class="text-center align-middle">
                    {{-- 🟢 بررسی اینکه آیا محصول متعلق به مدیر است یا فروشنده عادی --}}
                    @if($product->user?->hasRole('مدیر'))
                        <span class="badge badge-primary p-2"
                              style="font-size: 12px; font-weight: bold; border-radius: 6px;">
                        <i class="ti-world mr-1"></i> محصول سایت
                        </span>

                    @else

                        <span class="font-weight-bold text-dark">
                            {{ $product->user?->name ?? 'بدون نام' }}
                        </span>
                        <br>
                        <small class="text-muted d-block mt-1">
                            <i class="ti-mobile mr-1"></i> {{ $product->user?->mobile ?? '--' }}
                        </small>
                    @endif
                </td>

                <td class="align-middle">
                    <span class="badge badge-light border text-muted px-2 py-1">{{ $product->category->name ?? 'بدون دسته' }}</span>
                </td>
                <td class="align-middle">
                    <button class="btn btn-outline-info btn-xs btn-rounded-lux" wire:click="restoreProduct({{ $product->id }})">
                        <i class="ti-reload ml-1"></i> بازگردانی
                    </button>
                </td>
                <td class="align-middle">
                    @if(!$product->orderDetails()->exists())
                        <button class="btn btn-outline-danger btn-xs btn-rounded-lux"
                                wire:click="$dispatch('forceDeleteProduct', {'id': {{ $product->id }}})">
                            <i class="ti-trash ml-1"></i> حذف دائمی
                        </button>
                    @else
                        <span class="badge badge-soft-danger px-2 py-1 font-weight-normal small">دارای سابقه فروش</span>
                    @endif
                </td>
                <td class="align-middle text-secondary small">
                    {{ \Hekmatinasser\Verta\Verta::instance($product->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-5 bg-white border">
                    <div class="empty-state py-4">
                        <svg width="70" height="70" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h5 class="text-dark font-weight-bold">سطل زباله خالی است!</h5>
                        <p class="text-muted">محصولی با عبارت جستجوی <strong class="text-danger">"{{ $search }}"</strong> یافت نشد.</p>
                        @if($search)
                            <button wire:click="$set('search', '')" class="btn btn-primary btn-sm mt-2 px-3 shadow-sm">
                                <i class="ti-eraser ml-1"></i> پاکسازی فیلتر
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="mt-4 p-2 d-flex justify-content-center">
        {{ $products->appends(Request::except('page'))->links() }}
    </div>
</div>

@section('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // شنیدن رویداد تایید حذف دائمی
            Livewire.on('forceDeleteProduct', (event) => {
                const productId = event.id ? event.id : (event[0] ? event[0].id : null);

                Swal.fire({
                    title: "آیا از حذف دائمی و قطعی مطمئن هستید؟",
                    text: "این عملیات غیرقابل بازگشت است و فایل‌های محصول نیز کاملاً پاک خواهند شد!",
                    icon: "danger",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "بله، کاملاً پاک شود",
                    cancelButtonText: "خیر، انصراف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('destroy_trash_product', { id: productId });
                    }
                });
            });

            // کاملاً مستقل از توابع تودرتو برای بهینه‌سازی حافظه مرورگر
            Livewire.on('productDeletedTrash', () => {
                Swal.fire({
                    title: 'حذف دائمی شد!',
                    text: 'محصول برای همیشه از سیستم حذف گردید.',
                    icon: 'success',
                    confirmButtonText: 'تایید'
                });
            });

            Livewire.on('productArchivedTrash', () => {
                Swal.fire({
                    title: 'خطای عدم حذف قفل داده‌ها',
                    text: 'به دلیل وجود وابستگی‌های مالی و فاکتور فروش برای کاربران، امکان حذف فیزیکی این محصول وجود ندارد.',
                    icon: 'error',
                    confirmButtonText: 'تایید'
                });
            });
        });
    </script>
@endsection
