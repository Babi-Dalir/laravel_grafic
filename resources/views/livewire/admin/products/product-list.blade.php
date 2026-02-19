<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (نام محصول)</label>
        <div class="col-sm-6 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
        {{--        @hasallroles('مدیرکل')--}}
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
        {{--        @endhasanyrole--}}
        @hasallroles('فروشنده')
        <div class="col-sm-2">
            <a href="{{route('create.seller.product')}}" class="btn btn-outline-secondary">
                <i class="ti-plus">ایجاد محصول توسط فروشنده</i>
            </a>
        </div>
        @endhasanyrole
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">عکس</th>
            <th class="text-center align-middle text-primary">نام محصول</th>
            {{--            <th class="text-center align-middle text-primary">نام شرکت</th>--}}
            <th class="text-center align-middle text-primary">دسته بندی</th>
            <th class="text-center align-middle text-primary">ویژگی های محصول</th>
            <th class="text-center align-middle text-primary">گالری</th>
            {{--            @if(auth()->user()->is_admin)--}}
            <th class="text-center align-middle text-primary">وضعیت</th>
            {{--            @endif--}}
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($products as $index=>$product)
            <tr>
                <td class="text-center align-middle">{{$products->firstItem()+$index}}</td>
                <td class="text-center align-middle">
                    <figure class="avatar avatar">
                        <img src="{{url('images/products/small/'.$product->image)}}" class="rounded-circle" alt="image">
                    </figure>
                </td>
                <td class="text-center align-middle">{{$product->name}}</td>
                {{--                <td class="text-center align-middle">{{$product->user->seller?->company_name}}</td>--}}
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
                {{--                @if(auth()->user()->is_admin)--}}
                <td class="text-center align-middle">
                    <div wire:click="changeStatus({{$product->id}})" class="status-interactive-wrapper">

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

                {{--                @endif--}}
                <td class="text-center align-middle">
                    @if(auth()->user()->id == $product->user->id)
                        <a class="btn btn-outline-info" href="{{route('products.edit',$product->id)}}">
                            ویرایش
                        </a>
                    @endif
                </td>
                <td class="text-center align-middle">
                    @if(auth()->user()->id == $product->user->id)
                        <a class="btn btn-outline-danger"
                           wire:click="$dispatch('deleteProduct',{'id':{{$product->id}}})">
                            حذف
                        </a>
                    @endif
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($product->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        {{-- یک SVG ساده و شیک برای حالت جستجو --}}
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" class="mb-3">
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
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


