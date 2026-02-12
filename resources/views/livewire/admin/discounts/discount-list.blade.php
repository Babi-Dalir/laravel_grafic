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
            <th class="text-center align-middle text-primary">کد تخفیف</th>
            <th class="text-center align-middle text-primary">میزان تخفیف</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary"> تاریخ انقضا</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($discounts as $index=>$discount)
            <tr>
                <td class="text-center align-middle">{{$discounts->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$discount->code}}</td>
                <td class="text-center align-middle">{{number_format($discount->discount)}} تومان </td>

                <td class="text-center align-middle" wire:click="changeStatus({{$discount->id}})">
                    @if($discount->status === \App\Enums\DiscountStatus::Active->value)
                        <span class="cursor-pointer badge badge-success">فعال</span>
                    @elseif($discount->status === \App\Enums\DiscountStatus::InActive->value)
                        <span class="cursor-pointer badge badge-warning">غیر فعال</span>
                    @endif

                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($discount->expiration_date)->format('%d%B، %Y')}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteDiscount',{'id':{{$discount->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($discount->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$discounts->appends(Request::except('page'))->links()}}
    </div>
</div>

@section('scripts')
    <script>
        Livewire.on('deleteDiscount', (event) => {
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
                    Livewire.dispatch('destroy_discount',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


