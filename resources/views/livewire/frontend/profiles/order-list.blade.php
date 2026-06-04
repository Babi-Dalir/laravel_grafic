<div class="dt-sl">
    <div class="table-responsive">
        <table class="table table-order">
            <thead>
            <tr>
                <th>ردیف</th>
                <th>شماره سفارش</th>
                <th>تاریخ ثبت سفارش</th>
                <th>مبلغ قابل پرداخت</th>
                <th>میزان تخفیف</th>
                <th>وضعیت</th>
                <th>جزییات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $index=>$order)
                <tr>
                    <td>{{$orders->firstItem()+$index}}</td>
                    <td>{{$order->order_code}}</td>
                    <td>{{\Hekmatinasser\Verta\Verta::instance($order->created_at)->format('%d%B، %Y')}}</td>
                    <td>{{number_format($order->total_price)}} تومان</td>
                    <td>{{number_format($order->discount_price)}} تومان</td>
                    <td>
                        @if($order->status === \App\Enums\OrderStatus::WaitPayment->value)
                            <span class="cursor-pointer badge badge-warning">در انتظار پرداخت</span>
                        @elseif($order->status === \App\Enums\OrderStatus::Payed->value)
                            <span class="cursor-pointer badge badge-success">پرداخت شده</span>
                        @elseif($order->status === \App\Enums\OrderStatus::Failed->value)
                            <span class="cursor-pointer badge badge-danger">پرداخت ناموفق</span>
                        @elseif($order->status === \App\Enums\OrderStatus::Cancelled->value)
                            <span class="cursor-pointer badge badge-secondary">انصراف از پرداخت</span>
                        @endif
                    </td>
                    <td class="details-link">
                        <a href="{{route('profile.order.details',$order->id)}}">
                            <i class="mdi mdi-chevron-left"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="alert alert-warning mb-0">
                            هنوز محصولی خریداری نکرده‌اید.
                        </div>
                    </td>
                </tr>

            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $orders->links('vendor.pagination.profile-pagination.profile_orders') }}
    </div>
</div>
