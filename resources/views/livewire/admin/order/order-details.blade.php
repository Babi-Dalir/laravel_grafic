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
            <th class="text-center align-middle text-primary">نام محصول</th>
            <th class="text-center align-middle text-primary">رنگ</th>
            <th class="text-center align-middle text-primary">گارانتی</th>
            <th class="text-center align-middle text-primary">قیمت</th>
            <th class="text-center align-middle text-primary">تخفیف</th>
            <th class="text-center align-middle text-primary">تعداد</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order_details as $index=>$order_detail)
            <tr>
                <td class="text-center align-middle">{{$order_details->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$order_detail->product->name}}</td>
                <td class="text-center align-middle">{{$order_detail->color->name}}</td>
                <td class="text-center align-middle">{{$order_detail->guaranty->name}}</td>
                <td class="text-center align-middle">{{number_format($order_detail->price)}}تومان </td>
                <td class="text-center align-middle">{{$order_detail->discount}}%</td>
                <td class="text-center align-middle">{{$order_detail->count}}</td>
                <td class="text-center align-middle" wire:click="changeOrderDetailStatus({{$order_detail->id}})">
                    @if($order_detail->status === \App\Enums\OrderDetailStatus::Processing->value)
                        <span class="cursor-pointer badge badge-info">در حال پردازش</span>
                    @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Received->value)
                        <span class="cursor-pointer badge badge-success">دریافت شده</span>
                    @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Rejected->value)
                        <span class="cursor-pointer badge badge-danger">پس داده شده</span>
                    @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Waiting->value)
                        <span class="cursor-pointer badge badge-warning">در حال انتظار</span>
                    @endif

                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($order_detail->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$order_details->appends(Request::except('page'))->links()}}
    </div>
</div>



