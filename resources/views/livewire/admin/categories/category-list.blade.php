<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (عنوان نقش)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>
    @if($search_categories)
        @forelse($search_categories as $category)
            <table class="table table-striped table-hover">
                <thead class="thead-light">
                <tr>
                    <th class="text-center align-middle text-primary">عکس</th>
                    <th class="text-center align-middle text-primary">عنوان دسته بندی</th>
                    <th class="text-center align-middle text-primary">دسته پدر</th>
                    <th class="text-center align-middle text-primary">نام انگلیسی</th>
                    <th class="text-center align-middle text-primary">اسلاگ</th>
                    <th class="text-center align-middle text-primary">ویرایش</th>
                    <th class="text-center align-middle text-primary">حذف</th>
                    <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center align-middle">
                        <figure class="avatar avatar">
                            <img src="{{url('images/categories/small/'.$category->image)}}" class="rounded-circle"
                                 alt="image">
                        </figure>
                    </td>
                    <td class="text-center align-middle">{{$category->name}}</td>
                    <td class="text-center align-middle">{{$category->parentCategory->name}}</td>
                    <td class="text-center align-middle">{{$category->e_name}}</td>
                    <td class="text-center align-middle">{{$category->slug}}</td>
                    <td class="text-center align-middle">
                        <a class="btn btn-outline-info" href="{{route('categories.edit',$category->id)}}">
                            ویرایش
                        </a>
                    </td>
                    <td class="text-center align-middle">
                        <a class="btn btn-outline-danger"
                           wire:click="$dispatch('deleteCategory',{'id':{{$category->id}}})">
                            حذف
                        </a>
                    </td>
                    <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($category->created_at)->format('%d%B، %Y')}}</td>
                </tr>
            </table>
        @empty
            <div class="text-center py-5 w-100 shadow-sm border rounded bg-light">
                <div class="empty-state">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                    <p class="text-muted">دسته بندی با عبارت <strong class="text-danger">"{{ $search }}"</strong> در سیستم ثبت نشده است.</p>
                    <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="ti-eraser m-r-5"></i> پاکسازی جستجو
                    </button>
                </div>
            </div>
        @endforelse
    @else
        @forelse($categories as $category)
            <div class="accordion" id="accordion">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <button class="btn btn-link primary-font" type="button" data-toggle="collapse"
                                data-target="#collapseOne">
                            {{$category->parentCategory->name}}
                        </button>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                <tr>
                                    <th class="text-center align-middle text-primary">عکس</th>
                                    <th class="text-center align-middle text-primary">عنوان دسته بندی</th>
                                    <th class="text-center align-middle text-primary">دسته پدر</th>
                                    <th class="text-center align-middle text-primary">نام انگلیسی</th>
                                    <th class="text-center align-middle text-primary">اسلاگ</th>
                                    <th class="text-center align-middle text-primary">ویرایش</th>
                                    <th class="text-center align-middle text-primary">حذف</th>
                                    <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center align-middle">
                                        <figure class="avatar avatar">
                                            <img src="{{url('images/categories/small/'.$category->image)}}"
                                                 class="rounded-circle" alt="image">
                                        </figure>
                                    </td>
                                    <td class="text-center align-middle">{{$category->name}}</td>
                                    <td class="text-center align-middle">{{$category->parentCategory->name}}</td>
                                    <td class="text-center align-middle">{{$category->e_name}}</td>
                                    <td class="text-center align-middle">{{$category->slug}}</td>
                                    <td class="text-center align-middle">
                                        <a class="btn btn-outline-info"
                                           href="{{route('categories.edit',$category->id)}}">
                                            ویرایش
                                        </a>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a class="btn btn-outline-danger"
                                           wire:click="$dispatch('deleteCategory',{'id':{{$category->id}}})">
                                            حذف
                                        </a>
                                    </td>
                                    <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($category->created_at)->format('%d%B، %Y')}}</td>
                                </tr>
                            </table>
                            @foreach($category->childCategory()->get() as $cat1)
                                <div class="accordion" id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <button class="btn btn-link primary-font" type="button"
                                                    data-toggle="collapse" data-target="#collapseOne">
                                                {{$cat1->parentCategory->name}}
                                            </button>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                             data-parent="#accordion">
                                            <div class="card-body">
                                                <table class="table table-striped table-hover">
                                                    <thead class="thead-light">
                                                    <tr>
                                                        <th class="text-center align-middle text-primary">عکس</th>
                                                        <th class="text-center align-middle text-primary">عنوان دسته
                                                            بندی
                                                        </th>
                                                        <th class="text-center align-middle text-primary">دسته پدر</th>
                                                        <th class="text-center align-middle text-primary">نام انگلیسی
                                                        </th>
                                                        <th class="text-center align-middle text-primary">اسلاگ</th>
                                                        <th class="text-center align-middle text-primary">ویرایش</th>
                                                        <th class="text-center align-middle text-primary">حذف</th>
                                                        <th class="text-center align-middle text-primary">تاریخ ایجاد
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td class="text-center align-middle">
                                                            <figure class="avatar avatar">
                                                                <img
                                                                    src="{{url('images/categories/small/'.$cat1->image)}}"
                                                                    class="rounded-circle" alt="image">
                                                            </figure>
                                                        </td>
                                                        <td class="text-center align-middle">{{$cat1->name}}</td>
                                                        <td class="text-center align-middle">{{$cat1->parentCategory->name}}</td>
                                                        <td class="text-center align-middle">{{$cat1->e_name}}</td>
                                                        <td class="text-center align-middle">{{$cat1->slug}}</td>
                                                        <td class="text-center align-middle">
                                                            <a class="btn btn-outline-info"
                                                               href="{{route('categories.edit',$cat1->id)}}">
                                                                ویرایش
                                                            </a>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <a class="btn btn-outline-danger"
                                                               wire:click="$dispatch('deleteCategory',{'id':{{$cat1->id}}})">
                                                                حذف
                                                            </a>
                                                        </td>
                                                        <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($cat1->created_at)->format('%d%B، %Y')}}</td>
                                                    </tr>
                                                </table>
                                                <div class="w-75">
                                                    @foreach($cat1->childCategory()->get() as $cat2)
                                                        <div class="accordion" id="accordion">
                                                            <div class="card">
                                                                <div class="card-header" id="headingOne">
                                                                    <button class="btn btn-link primary-font"
                                                                            type="button" data-toggle="collapse"
                                                                            data-target="#collapseOne">
                                                                        {{$cat2->parentCategory->name}}
                                                                    </button>
                                                                </div>
                                                                <div id="collapseOne" class="collapse show"
                                                                     aria-labelledby="headingOne"
                                                                     data-parent="#accordion">
                                                                    <div class="card-body">
                                                                        <table class="table table-striped table-hover">
                                                                            <thead class="thead-light">
                                                                            <tr>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    عکس
                                                                                </th>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    عنوان دسته بندی
                                                                                </th>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    دسته پدر
                                                                                </th>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    نام انگلیسی
                                                                                </th>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    اسلاگ
                                                                                </th>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    ویرایش
                                                                                </th>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    حذف
                                                                                </th>
                                                                                <th class="text-center align-middle text-primary">
                                                                                    تاریخ ایجاد
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <tr>
                                                                                <td class="text-center align-middle">
                                                                                    <figure class="avatar avatar">
                                                                                        <img
                                                                                            src="{{url('images/categories/small/'.$cat2->image)}}"
                                                                                            class="rounded-circle"
                                                                                            alt="image">
                                                                                    </figure>
                                                                                </td>
                                                                                <td class="text-center align-middle">{{$cat2->name}}</td>
                                                                                <td class="text-center align-middle">{{$cat2->parentCategory->name}}</td>
                                                                                <td class="text-center align-middle">{{$cat2->e_name}}</td>
                                                                                <td class="text-center align-middle">{{$cat2->slug}}</td>
                                                                                <td class="text-center align-middle">
                                                                                    <a class="btn btn-outline-info"
                                                                                       href="{{route('categories.edit',$cat2->id)}}">
                                                                                        ویرایش
                                                                                    </a>
                                                                                </td>
                                                                                <td class="text-center align-middle">
                                                                                    <a class="btn btn-outline-danger"
                                                                                       wire:click="$dispatch('deleteCategory',{'id':{{$cat2->id}}})">
                                                                                        حذف
                                                                                    </a>
                                                                                </td>
                                                                                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($cat2->created_at)->format('%d%B، %Y')}}</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 w-100 shadow-sm border rounded bg-light">
                <div class="empty-state">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <h5 class="text-dark" style="font-weight: 600;">هیچ دسته‌بندی یافت نشد!</h5>
                    <p class="text-muted">در حال حاضر هیچ داده‌ای برای نمایش وجود ندارد.</p>
                </div>
            </div>
        @endforelse
    @endif
</div>
@section('scripts')
    <script>
        Livewire.on('deleteCategory', (event) => {
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
                    Livewire.dispatch('destroy_category',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


