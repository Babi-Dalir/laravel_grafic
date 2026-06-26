<div class="dt-sl">
    <div class="table-responsive">
        <table class="table table-order">
            <thead>
            <tr>
                <th>ردیف</th>
                <th>نام محصول</th>
                <th>مبلغ پرداخت شده</th>
                <th>تخفیف</th>
                <th>وضعیت</th>
                <th>تعداد دانلود</th>
                <th>دانلود</th>
            </tr>
            </thead>
            <tbody>
            @forelse($order_details as $index => $order_detail)
                <tr>
                    <td>{{ $order_details->firstItem() + $index }}</td>
                    <td>{{ $order_detail->product?->name ?? 'محصول حذف شده' }}</td>
                    <td>{{ number_format($order_detail->price) }} تومان</td>
                    <td>{{ number_format($order_detail->discount) }} تومان</td>
                    <td>
                        @if($order_detail->status === \App\Enums\OrderDetailStatus::Paid->value)
                            <span class="badge badge-success">پرداخت شده</span>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Downloaded->value)
                            <span class="badge badge-info">کاملا دانلود شده</span>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Refunded->value)
                            <span class="badge badge-danger">مرجوع شده</span>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Waiting->value)
                            <span class="badge badge-warning">در حال انتظار</span>
                        @endif
                    </td>

                    <td class="text-center align-middle">
                        {{-- 🟢 اصلاح باگ: جلوگیری از خطای کرش با بهره‌گیری از تمپلیت امن --}}
                        @if($order_detail->download)
                            {{ $order_detail->download->download_count }} / {{ $order_detail->download->max_download }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if($order_detail->download)
                            @if($order_detail->download->download_count < $order_detail->download->max_download)
                                <a href="{{ route('download.file', $order_detail->download->token) }}" class="btn btn-success">
                                    دانلود فایل
                                </a>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>
                                    سقف دانلود تکمیل شده
                                </button>
                            @endif
                        @else
                            <span class="text-muted text-sm">فاقد فایل دانلود</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="alert alert-warning mb-0">
                            آیتمی برای این سفارش یافت نشد.
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $order_details->links('vendor.pagination.profile-pagination.profile_order_details') }}
    </div>
</div>
