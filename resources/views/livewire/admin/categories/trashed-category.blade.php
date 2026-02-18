<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <div class="col-sm-12 d-flex align-items-center">
            <label class="col-sm-2 col-form-label">جستجو (عنوان دسته بندی)</label>
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
            <div class="col-sm-2">
                <a href="{{ route('categories.index') }}" class="btn-list-modern">
                    <div class="icon-box-info">
                        <i class="ti-list"></i>
                    </div>
                    <div class="text-content">
                        <span class="title">لیست دسته بندی ها</span>
                        <span class="subtitle">مشاهده همه</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">عکس</th>
            <th class="text-center align-middle text-primary">عنوان دسته بندی</th>
            <th class="text-center align-middle text-primary">دسته پدر</th>
            <th class="text-center align-middle text-primary">نام انگلیسی</th>
            <th class="text-center align-middle text-primary">اسلاگ</th>
            <th class="text-center align-middle text-primary">بازگردانی</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $index=>$category)
            <tr>
                <td class="text-center align-middle">{{$categories->firstItem()+$index}}</td>
                <td class="text-center align-middle">
                    <figure class="avatar avatar">
                        <img src="{{url('images/categories/small/'.$category->image)}}" class="rounded-circle" alt="image">
                    </figure>
                </td>
                <td class="text-center align-middle">{{$category->name}}</td>
                <td class="text-center align-middle">{{$category->parentCategory->name}}</td>
                <td class="text-center align-middle">{{$category->e_name}}</td>
                <td class="text-center align-middle">{{$category->slug}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-info" wire:click="restoreCategory({{$category->id}})">
                        بازگردانی
                    </a>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('forceDeleteCategory',{'id':{{$category->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($category->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @empty
            <div class="text-center py-5 w-100 shadow-sm border rounded bg-light">
                <div class="empty-state">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                         stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                    <p class="text-muted">دسته بندی با عبارت <strong class="text-danger">"{{ $search }}"</strong> در
                        سیستم ثبت نشده است.</p>
                    <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="ti-eraser m-r-5"></i> پاکسازی جستجو
                    </button>
                </div>
            </div>
        @endforelse
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$categories->appends(Request::except('page'))->links()}}
    </div>
</div>
@section('scripts')
    <script>
        Livewire.on('forceDeleteCategory', (event) => {
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
                    Livewire.dispatch('destroy_trash_category',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


