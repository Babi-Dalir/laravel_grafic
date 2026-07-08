<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (کد,عنوان کارت هدیه,نام کاربر)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">کد کارت هدیه</th>
            <th class="text-center align-middle text-primary">مقدار کل کارت هدیه</th>
            <th class="text-center align-middle text-primary">باقی مانده کارت هدیه</th>
            <th class="text-center align-middle text-primary">عنوان کارت هدیه</th>
            <th class="text-center align-middle text-primary">نام کاربر</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary"> تاریخ انقضا</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($gift_carts as $index=>$gift_cart)
            <tr>
                <td class="text-center align-middle">{{$gift_carts->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$gift_cart->code}}</td>
                <td class="text-center align-middle">{{number_format($gift_cart->gift_price)}} تومان</td>
                <td class="text-center align-middle">{{number_format($gift_cart->balance)}} تومان</td>
                <td class="text-center align-middle">{{$gift_cart->gift_title}}</td>
                <td class="text-center align-middle">{{$gift_cart->user?->name ?? 'کاربر حذف شده'}}</td>

                <td class="text-center align-middle">
                    <div class="status-interactive-wrapper" wire:click="changeStatus({{$gift_cart->id}})" style="cursor: pointer;">
                        {{-- 🟢 اصلاح: یکپارچه‌سازی بررسی رشته انوم فعال با الگوهای مدل --}}
                        @if($gift_cart->status->value === \App\Enums\GiftCartStatus::Active->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i>
                                <span>فعال</span>
                            </div>
                        @else
                            <div class="modern-status-btn inactive">
                                <i class="ti-power-off mr-1"></i>
                                <span>غیرفعال</span>
                            </div>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle">
                    @if($gift_cart->expiration_date)
                        {{ \Hekmatinasser\Verta\Verta::instance($gift_cart->expiration_date)->format('%d %B، %Y') }}

                        @if(now()->greaterThan($gift_cart->expiration_date))
                            <br><span class="badge badge-danger small">منقضی شده</span>
                        @endif
                    @else
                        <span class="text-muted">بدون انقضا</span>
                    @endif
                </td>

                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger"
                       wire:click="$dispatch('deleteGiftCart',{'id':{{$gift_cart->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($gift_cart->created_at)->format('%d %B، %Y')}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>

                        <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                        <p class="text-muted">کارت هدیه ای با عبارت <strong class="text-danger">"{{ $search }}"</strong> در سیستم ثبت نشده است.</p>

                        @if($search)
                            <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="ti-eraser m-r-5"></i> پاکسازی جستجو
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
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
                    Livewire.dispatch('destroy_gift_cart', {id: event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        });

        Livewire.on('showToastError', (event) => {
            Swal.fire({
                title: "خطا!",
                text: event.message,
                icon: "error",
                confirmButtonText: "متوجه شدم"
            });
        });
    </script>
@endsection
