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
            <th class="text-center align-middle text-primary">کد کارت هدیه</th>
            <th class="text-center align-middle text-primary">میزان کارت هدیه</th>
            <th class="text-center align-middle text-primary">عنوان کارت هدیه</th>
            <th class="text-center align-middle text-primary">نام کاربر</th>
            <th class="text-center align-middle text-primary"> تاریخ انقضا</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($gift_carts as $index=>$gift_cart)
            <tr>
                <td class="text-center align-middle">{{$gift_carts->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$gift_cart->code}}</td>
                <td class="text-center align-middle">{{number_format($gift_cart->gift_price)}} تومان </td>
                <td class="text-center align-middle">{{$gift_cart->gift_title}}</td>
                <td class="text-center align-middle">{{$gift_cart->user->name}}</td>

                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($gift_cart->expiration_date)->format('%d%B، %Y')}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteGiftCart',{'id':{{$gift_cart->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($gift_cart->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$gift_carts->appends(Request::except('page'))->links()}}
    </div>
</div>

@section('scripts')
    <script>
        Livewire.on('deleteGiftCart', (event) => {
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
                    Livewire.dispatch('destroy_gift_cart',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


