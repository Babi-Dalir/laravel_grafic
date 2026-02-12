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
            <th class="text-center align-middle text-primary">نام کاربر</th>
            <th class="text-center align-middle text-primary">کد تحویل</th>
            <th class="text-center align-middle text-primary">کد پستی</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary">جزئیات سفارش</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $index=>$order)
            <tr>
                <td class="text-center align-middle">{{$orders->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$order->user->name}}</td>
                <td class="text-center align-middle">{{$order->order_code}}</td>
                <td class="text-center align-middle">{{$order->post_number}}</td>
                <td class="text-center align-middle" wire:click="changeOrderStatus({{$order->id}})">
                    @if($order->status === \App\Enums\OrderStatus::WaitPayment->value)
                        <span class="cursor-pointer badge badge-warning">در انتظار پرداخت</span>
                    @elseif($order->status === \App\Enums\OrderStatus::Payed->value)
                        <span class="cursor-pointer badge badge-success">پرداخت شده</span>
                    @elseif($order->status === \App\Enums\OrderStatus::Failed->value)
                        <span class="cursor-pointer badge badge-danger">انصراف ار پرداخت</span>
                    @elseif($order->status === \App\Enums\OrderStatus::ReceivedOrder->value)
                        <span class="cursor-pointer badge badge-success">سفارش دریافت شد</span>
                    @elseif($order->status === \App\Enums\OrderStatus::Processing->value)
                        <span class="cursor-pointer badge badge-info">در حال پردازش</span>
                    @elseif($order->status === \App\Enums\OrderStatus::SendOrder->value)
                        <span class="cursor-pointer badge badge-success">ارسال شده</span>
                    @elseif($order->status === \App\Enums\OrderStatus::NotReceivedOrder->value)
                        <span class="cursor-pointer badge badge-danger">سفارش دریافت نشد</span>
                    @endif

                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" href="{{route('admin.order.details.list',$order->id)}}">
                        جزئیات سفارش
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($order->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$orders->appends(Request::except('page'))->links()}}
    </div>
</div>



