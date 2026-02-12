<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">عنوان جستجو</label>
        <div class="col-sm-10">
            <input type="text" @keyup.enter="$wire.searchData" class="form-control text-left" dir="rtl" wire:model="search">
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">نام انبار</th>
            <th class="text-center align-middle text-primary"> وضعیت</th>
            <th class="text-center align-middle text-primary">لیست محصولات</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($depots as $index=>$depot)
            <tr>
                <td class="text-center align-middle">{{$depots->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$depot->name}}</td>
                <td class="text-center align-middle" wire:click="changeStatus({{$depot->id}})">
                    @if($depot->status === \App\Enums\DepotStatus::Active->value)
                        <span class="cursor-pointer badge badge-success">فعال</span>
                    @elseif($depot->status === \App\Enums\DepotStatus::InActive->value)
                        <span class="cursor-pointer badge badge-warning">غیر فعال</span>
                    @endif

                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-dark" href="{{route('add.product.in.depot',$depot->id)}}">
                        لیست محصولات
                    </a>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-info" href="{{route('depots.edit',$depot->id)}}">
                        ویرایش
                    </a>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteDepot',{'id':{{$depot->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($depot->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$depots->appends(Request::except('page'))->links()}}
    </div>
</div>
@section('scripts')
    <script>
        Livewire.on('deleteDepot', (event) => {
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
                    Livewire.dispatch('destroy_depot',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection
