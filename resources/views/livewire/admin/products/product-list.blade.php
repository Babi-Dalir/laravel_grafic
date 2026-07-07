<div class="table overflow-auto" tabindex="8">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (نام محصول)</label>
        <div class="col-sm-6 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
        <div class="col-sm-2">
            <a href="{{route('products.trashed')}}" class="btn btn-outline-warning">
                <i class="ti-trash">لیست محصولات حذف شده</i>
            </a>
        </div>
        <div class="col-sm-2">
            <a href="{{route('products.create')}}" class="btn btn-outline-secondary">
                <i class="ti-plus">ایجاد محصول</i>
            </a>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">عکس</th>
            <th class="text-center align-middle text-primary">نام محصول</th>
            <th class="text-center align-middle text-primary">نام فروشنده</th>
            <th class="text-center align-middle text-primary">دسته بندی</th>
            <th class="text-center align-middle text-primary">ویژگی های محصول</th>
            <th class="text-center align-middle text-primary">گالری</th>
            <th class="text-center align-middle text-primary">فایل محصول</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary">عملیات</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
            <th class="text-center align-middle text-primary">تکمیل محصول</th>
        </tr>
        </thead>
        <tbody>
        @forelse($products as $index=>$product)
            <tr>
                <td class="text-center align-middle">{{$products->firstItem()+$index}}</td>
                <td class="text-center align-middle">

                    <img
                        src="{{ url('images/products/small/'.$product->image) }}"
                        width="60"
                        class="rounded">

                </td>
                <td class="text-center align-middle">{{$product->name}}</td>

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
                <td class="text-center align-middle">{{$product->category->name}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-secondary" href="{{route('create.product.properties',$product)}}">
                        ویژگی های محصول
                    </a>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-success" href="{{route('add.product.gallery',$product->id)}}">
                        گالری
                    </a>
                </td>

                <td class="text-center align-middle">

                    @if($product->files->count())

                        <a
                            href="{{ route('product.file.list',$product) }}"
                            class="btn btn-outline-info">

                            فایل‌ها ({{ $product->files->count() }})

                        </a>

                    @else

                        <span class="badge badge-secondary">
                            بدون فایل
                        </span>

                    @endif

                </td>

                <td class="text-center align-middle">
                    <div
                        @role('مدیر')
                        wire:click="changeStatus({{ $product->id }})"
                        style="cursor:pointer"
                        class="status-interactive-wrapper"
                        @endrole
                    >

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

                <td class="text-center">

                    @role('مدیر')
                    @if($product->status === \App\Enums\ProductStatus::PendingReview->value)

                        <button
                            wire:click="approveProductRequest({{ $product->id }})"
                            class="btn btn-success btn-sm">
                            تایید
                        </button>

                        <button
                            class="btn btn-danger btn-sm"
                            wire:click="$set('productRequestId', {{ $product->id }})"
                            data-toggle="modal"
                            data-target="#rejectModal">
                            رد
                        </button>

                    @endif
                    @endrole

                </td>

                <td class="text-center align-middle">
                    @if(auth()->user()->hasRole('مدیر') || auth()->user()->id == $product->user_id)
                        <a class="btn btn-outline-info" href="{{route('products.edit',$product->id)}}">
                            ویرایش
                        </a>
                    @endif
                </td>
                <td class="text-center align-middle">
                    @if(auth()->user()->hasRole('مدیر') || auth()->user()->id == $product->user_id)
                        <a class="btn btn-outline-danger"
                           wire:click="$dispatch('deleteProduct',{'id':{{$product->id}}})">
                            حذف
                        </a>
                    @endif
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($product->created_at)->format('%d%B، %Y')}}</td>

                <td class="text-center align-middle">

                    @php
                        $percent = $product->completion_percent;
                    @endphp

                    <div class="progress">

                        <div
                            class="progress-bar
                            {{ $percent == 100 ? 'bg-success' : ($percent >= 70 ? 'bg-info' : ($percent >= 40 ? 'bg-warning' : 'bg-danger')) }}"
                            style="width: {{ $percent }}%">

                            {{ $percent }}%

                        </div>

                    </div>

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
    </table>
    <div
        wire:ignore.self
        class="modal fade"
        id="rejectModal"
        tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        دلیل رد درخواست
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                <textarea
                    wire:model="review_note"
                    rows="5"
                    class="form-control"
                    placeholder="دلیل رد درخواست را وارد کنید...">
                </textarea>

                </div>

                <div class="modal-footer">

                    <button
                        wire:click="rejectProductRequest"
                        class="btn btn-danger">

                        ثبت و رد درخواست

                    </button>

                </div>

            </div>

        </div>

    </div>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$products->appends(Request::except('page'))->links()}}
    </div>
</div>
@section('scripts')
    <script>
        Livewire.on('deleteProduct', (event) => {
            Swal.fire({
                title: "آیا از حذف مطمئن هستید؟",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "بله",
                cancelButtonText: "خیر",
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('destroy_product', {id: event.id})
                    Livewire.on('productDeleted', () => {
                        Swal.fire({
                            title: 'محصول حذف شد',
                            icon: 'success'
                        });
                    });

                    Livewire.on('productArchived', () => {
                        Swal.fire({
                            title: 'محصول قبلا فروش داشته',
                            text: 'به جای حذف، آرشیو شد',
                            icon: 'info'
                        });
                    });
                }
            });
        })


        document.addEventListener('livewire:init', () => {

            Livewire.on('closeRejectModal', () => {

                console.log('EVENT FIRED');

                $('#rejectModal').modal('hide');

            });

        });
    </script>
@endsection


