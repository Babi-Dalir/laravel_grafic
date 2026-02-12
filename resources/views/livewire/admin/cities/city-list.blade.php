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
            <th class="text-center align-middle text-primary">نام استان</th>
            <th class="text-center align-middle text-primary">نام شهر</th>
            <th class="text-center align-middle text-primary">زمان ارسال</th>
            <th class="text-center align-middle text-primary">هزینه ارسال</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($cities as $index=>$city)
            <tr>
                <td class="text-center align-middle">{{$cities->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$city->province->province}}</td>
                <td class="text-center align-middle">{{$city->city}}</td>
                <td class="text-center align-middle">{{$city->send_time}}</td>
                <td class="text-center align-middle">{{$city->send_price}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-info" href="{{route('cities.edit',$city->id)}}">
                        ویرایش
                    </a>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteCity',{'id':{{$city->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($city->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$cities->appends(Request::except('page'))->links()}}
    </div>
</div>
@section('scripts')
    <script>
        Livewire.on('deleteCity', (event) => {
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
                    Livewire.dispatch('destroy_city',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


