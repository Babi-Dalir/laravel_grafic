<div class="table overflow-auto" tabindex="8">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="form-group row align-items-center mb-4">
        <label class="col-sm-2 col-form-label font-weight-bold">جستجوی سفارش:</label>
        <div class="col-sm-10 position-relative">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="کد سفارش، نام، موبایل یا ایمیل کاربر...">
            <div wire:loading wire:target="search" class="spinner-border spinner-border-sm text-primary position-absolute" style="left: 25px; top: 12px;"></div>
        </div>
    </div>

    <table class="table table-striped table-hover bg-white border">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">نام کاربر</th>
            <th class="text-center align-middle text-primary">کد سفارش</th>
            <th class="text-center align-middle text-primary">وضعیت و عملیات</th>
            <th class="text-center align-middle text-primary">جزئیات سفارش</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $index => $order)
            <tr>
                <td class="text-center align-middle font-weight-bold">{{ $orders->firstItem() + $index }}</td>
                {{-- 🟢 رفع باگ N+1 پتانسیل با Null-safe operator --}}
                <td class="text-center align-middle">{{ $order->user?->name ?? 'کاربر حذف شده' }}</td>
                <td class="text-center align-middle font-weight-bold text-secondary">{{ $order->order_code }}</td>

                {{-- 🟢 اصلاح UX: تبدیل تگ وضعیت به سیستم مدیریت اکشن تفکیک‌شده به جای کلیک بر روی کل سلول --}}
                <td class="text-center align-middle">
                    <div class="btn-group">
                        @if($order->status === \App\Enums\OrderStatus::WaitPayment)
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-hashtml="true" aria-expanded="false">
                                <i class="ti-timer ml-1"></i> در انتظار پرداخت
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-success" wire:click="changeOrderStatus({{ $order->id }}, 'pay')"><i class="ti-check ml-1"></i> تایید و ثبت پرداخت</button>
                                <button class="dropdown-item text-danger" wire:click="changeOrderStatus({{ $order->id }}, 'cancel')"><i class="ti-close ml-1"></i> لغو سفارش</button>
                            </div>
                        @elseif($order->status === \App\Enums\OrderStatus::Payed)
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-hashtml="true" aria-expanded="false">
                                <i class="ti-check ml-1"></i> پرداخت شده
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-danger" wire:click="changeOrderStatus({{ $order->id }}, 'fail')"><i class="ti-alert ml-1"></i> اعلام خطای تراکنش مالی</button>
                            </div>
                        @elseif($order->status === \App\Enums\OrderStatus::Failed)
                            <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-hashtml="true" aria-expanded="false">
                                <i class="ti-close ml-1"></i> پرداخت ناموفق
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-primary" wire:click="changeOrderStatus({{ $order->id }}, 'retry')"><i class="ti-reload ml-1"></i> بازگردانی به انتظار پرداخت</button>
                            </div>
                        @elseif($order->status === \App\Enums\OrderStatus::Cancelled)
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-hashtml="true" aria-expanded="false">
                                <i class="ti-na ml-1"></i> انصراف از پرداخت
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-primary" wire:click="changeOrderStatus({{ $order->id }}, 'renew')"><i class="ti-reload ml-1"></i> بازگشایی مجدد سفارش</button>
                            </div>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle">
                    <a class="btn btn-outline-info btn-sm px-3" href="{{ route('admin.order.details.list', $order->id) }}">
                        <i class="ti-eye ml-1"></i> مشاهده جزئیات
                    </a>
                </td>
                <td class="text-center align-middle text-muted small">
                    {{ \Hekmatinasser\Verta\Verta::instance($order->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5 bg-light">
                    <h6 class="text-muted">هیچ سفارشی با مشخصات مورد نظر یافت نشد.</h6>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin: 40px !important;" class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{ $orders->appends(Request::except('page'))->links() }}
    </div>
</div>
