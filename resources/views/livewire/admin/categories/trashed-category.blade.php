<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">عنوان جستجو</label>
        <div class="col-sm-8">
            <input type="text" @keyup.enter="$wire.searchData" class="form-control text-left" dir="rtl" wire:model="search">
        </div>
        <div class="col-sm-2">
            <a href="{{route('categories.index')}}" class="btn btn-outline-info">
                <i>لیست دسته بندی ها</i>
            </a>
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
        @foreach($categories as $index=>$category)
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
        @endforeach
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


