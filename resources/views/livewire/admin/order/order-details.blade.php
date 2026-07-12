<div class="table overflow-auto" tabindex="8">
    <div class="form-group row align-items-center mb-4">
        <label class="col-sm-2 col-form-label font-weight-bold">عنوان جستجو محصول:</label>
        <div class="col-sm-10 position-relative">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="نام محصول را جهت فیلتر تایپ کنید...">
            <div wire:loading wire:target="search"
                 class="spinner-border spinner-border-sm text-primary position-absolute"
                 style="left: 25px; top: 12px;"></div>
        </div>
    </div>

    <table class="table table-striped table-hover bg-white border">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">مشخصات فروشنده</th>
            <th class="text-center align-middle text-primary">نام محصول</th>
            <th class="text-center align-middle text-primary">قیمت پرداختی</th>
            <th class="text-center align-middle text-primary">تخفیف محصول</th>
            <th class="text-center align-middle text-primary">کد تخفیف (کوپن)</th> <!-- 🟢 اضافه شد -->
            <th class="text-center align-middle text-primary">درصد کمیسیون</th>
            <th class="text-center align-middle text-primary">سهم سایت</th>
            <th class="text-center align-middle text-primary">سوبسید پلتفرم</th> <!-- 🟢 اضافه شد -->
            <th class="text-center align-middle text-primary">سهم فروشنده</th>
            <th class="text-center align-middle text-primary">وضعیت و عملیات</th>
            <th class="text-center align-middle text-primary">تعداد دانلود</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        <tbody>
        @forelse($order_details as $index => $order_detail)
            @php
                $product = $order_detail->product;
                $productOwner = $product?->user; // واکشی مستقیم کاربرِ ایجادکننده محصول

                // 🟢 بررسی هوشمند و دقیق ساختار مالکیت محصول بر اساس منطق مارکت‌پلیس شما
                $isWebsiteProduct = ($productOwner && $productOwner->hasRole('مدیر'));

                $sellerName = '---';
                $sellerMobile = '---';

                if (!$isWebsiteProduct) {
                    if ($product?->seller) {
                        $sellerName = $product->seller->brand_name ?? ($product->seller->first_name . ' ' . $product->seller->last_name);
                        $sellerMobile = $product->seller->user?->mobile ?? '---';
                    } elseif ($productOwner) {
                        $sellerName = $productOwner->name;
                        $sellerMobile = $productOwner->mobile;
                    }
                }
            @endphp
            <tr wire:key="order-detail-row-{{ $order_detail->id }}">
                <td class="text-center align-middle font-weight-bold font-numeric">{{ $order_details->firstItem() + $index }}</td>

                {{-- 🎯 ستون مشخصات فروشنده یا محصول مستقیم سایت --}}
                <td class="text-center align-middle">
                    @if($isWebsiteProduct)
                        <span class="badge badge-primary p-2 shadow-sm"
                              style="font-size: 11px; font-weight: bold; border-radius: 6px;">
                            <i class="ti-world mr-1"></i> محصول سایت
                        </span>
                    @else
                        <div>
                            <strong class="text-dark">
                                {{ $sellerName }}
                            </strong>
                            <br>
                            <small class="text-muted font-numeric d-block mt-1">
                                <i class="ti-mobile mr-1"></i> {{ $sellerMobile }}
                            </small>
                        </div>
                    @endif
                </td>

                <td class="text-center align-middle font-weight-bold text-right">
                    {{ $product?->name ?? 'محصول حذف شده' }}
                    @if($product?->trashed())
                        <span class="badge badge-danger small">حذف شده</span>
                    @endif
                </td>
                <td class="text-center align-middle text-success font-weight-bold font-numeric">
                    {{ number_format($order_detail->price) }} تومان
                </td>
                <td class="text-center align-middle text-danger font-numeric">
                    {{ number_format($order_detail->discount) }} تومان
                </td>

                <!-- 🟢 نمایش فیلد کد تخفیف اعمال شده روی آیتم -->
                <td class="text-center align-middle text-danger font-numeric">
                    @if($order_detail->coupon_discount > 0)
                        <span class="text-danger font-weight-bold">{{ number_format($order_detail->coupon_discount) }} تومان</span>
                    @else
                        <span class="text-muted">0</span>
                    @endif
                </td>

                <td class="text-center align-middle text-secondary font-numeric">
                    {{ number_format($product?->category?->commission?->commission_percent ?? 20) }} %
                </td>
                <td class="text-center align-middle text-info font-numeric">
                    {{ number_format($order_detail->site_share) }} تومان
                </td>

                <!-- 🟢 نمایش سوبسید با استایل برجسته برای هوشیاری مدیر سایت -->
                <td class="text-center align-middle font-numeric">
                    @if($order_detail->platform_subsidy > 0)
                        <span class="badge badge-danger p-2 shadow-sm" style="animation: pulse 2s infinite;">
                            {{ number_format($order_detail->platform_subsidy) }} تومان سوبسید
                        </span>
                    @else
                        <span class="text-muted">0</span>
                    @endif
                </td>

                <td class="text-center align-middle text-primary font-weight-bold font-numeric">
                    {{ number_format($order_detail->seller_share) }} تومان
                </td>

                {{-- مدیریت وضعیت بر پایه دکمه‌های تفکیک شده دراپ‌داون --}}
                <td class="text-center align-middle">
                    <div class="btn-group">
                        @if($order_detail->status === \App\Enums\OrderDetailStatus::Waiting || $order_detail->status->value === 'waiting')
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                در حال انتظار
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-success"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'pay')"><i
                                        class="ti-check ml-1"></i> تغییر به پرداخت شده
                                </button>
                                <button class="dropdown-item text-danger"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'refund')"><i
                                        class="ti-back-left ml-1"></i> مرجوع کردن آیتم
                                </button>
                            </div>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Paid || $order_detail->status->value === 'paid')
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                پرداخت شده
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-info"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'download')"><i
                                        class="ti-download ml-1"></i> تغییر به دانلود شده
                                </button>
                                <button class="dropdown-item text-danger"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'refund')"><i
                                        class="ti-back-left ml-1"></i> مرجوع کردن آیتم
                                </button>
                            </div>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Downloaded || $order_detail->status->value === 'downloaded')
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                کاملا دانلود شده
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-danger"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'refund')"><i
                                        class="ti-back-left ml-1"></i> مرجوع کردن آیتم
                                </button>
                            </div>
                        @elseif($order_detail->status === \App\Enums\OrderDetailStatus::Refunded || $order_detail->status->value === 'refunded')
                            <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                مرجوع شده
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item text-warning"
                                        wire:click="changeOrderDetailStatus({{ $order_detail->id }}, 'reset')"><i
                                        class="ti-reload ml-1"></i> بازگردانی به حالت انتظار
                                </button>
                            </div>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle font-weight-bold text-secondary font-numeric">
                    {{ $order_detail->download?->download_count ?? 0 }}
                    / {{ $order_detail->download?->max_download ?? 0 }}
                </td>
                <td class="text-center align-middle text-muted small font-numeric">
                    {{ \Hekmatinasser\Verta\Verta::instance($order_detail->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <!-- 🟢 تغییر تعداد colspan به 13 ستون -->
                <td colspan="13" class="text-center py-5 bg-light text-muted">
                    هیچ جزئیاتی برای این سفارش یافت نشد.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{ $order_details->appends(Request::except('page'))->links() }}
    </div>
</div>
