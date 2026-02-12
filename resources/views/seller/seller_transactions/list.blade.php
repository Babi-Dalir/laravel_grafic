@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        <div class="card">
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-4 border-end">
                        <div class="text-muted small">مبلغ واریز شده</div>
                        <div class="fw-bold text-success">{{$deposit}}</div>
                    </div>
                    <div class="col-4 border-end">
                        <div class="text-muted small">مبلغ برداشت شده</div>
                        <div class="fw-bold text-danger">{{$withdraw}}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">کل موجودی</div>
                        <div class="fw-bold text-info">{{$total_money}}</div>
                    </div>
                </div>
                <div class="table overflow-auto" tabindex="8">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                        <tr>
                            <th class="text-center align-middle text-primary">ردیف</th>
                            <th class="text-center align-middle text-primary">مبلغ</th>
                            <th class="text-center align-middle text-primary">کد سفارش</th>
                            <th class="text-center align-middle text-primary">نوع تراکنش</th>
                            <th class="text-center align-middle text-primary">توضیحات</th>
                            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($user_transactions as $index=>$user_transaction)
                            <tr>
                                <td class="text-center align-middle">{{$user_transactions->firstItem()+$index}}</td>
                                <td class="text-center align-middle">{{number_format($user_transaction->money)}}
                                    تومان
                                </td>
                                <td class="text-center align-middle">{{$user_transaction->order->order_code}}</td>
                                <td class="text-center align-middle">
                                    @if($user_transaction->type === \App\Enums\TransactionType::Deposit->value)
                                        <span class="cursor-pointer badge badge-success">واریز شده</span>
                                    @elseif($user_transaction->type === \App\Enums\TransactionType::Withdraw->value)
                                        <span class="cursor-pointer badge badge-danger">برداشت شده</span>
                                    @endif

                                </td>
                                <td class="text-center align-middle">{{$user_transaction->description}}</td>
                                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($user_transaction->created_at)->format('%d%B، %Y')}}</td>
                            </tr>
                        @endforeach
                    </table>
                    <div style="margin: 40px !important;"
                         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
                        {{$user_transactions->appends(Request::except('page'))->links()}}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
