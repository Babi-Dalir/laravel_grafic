<div class="table overflow-auto" tabindex="8">
    {{-- هدر آلرت وضعیت تغییرات سیستم --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show text-left" role="alert">
            <i class="ti-check mr-2"></i> {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show text-left" role="alert">
            <i class="ti-close mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (نام، برند، کد ملی، موبایل)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="مشخصات فروشنده را تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    <table class="table table-striped table-hover align-middle">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">عکس</th>
            <th class="text-center align-middle text-primary">نام و نام خانوادگی</th>
            <th class="text-center align-middle text-primary">نام کاربری</th>
            <th class="text-center align-middle text-primary">موبایل</th>
            <th class="text-center align-middle text-primary">نام برند</th>
            <th class="text-center align-middle text-primary">کد ملی</th>
            <th class="text-center align-middle text-primary">وضعیت دسترسی و بانک</th>
            <th class="text-center align-middle text-primary">جزئیات پروژه‌ای</th>
            <th class="text-center align-middle text-primary">تاریخ ثبت‌نام</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sellers as $index => $seller)
            <tr wire:key="seller-row-{{ $seller->id }}">
                <td class="text-center align-middle class-row-number">{{ $sellers->firstItem() + $index }}</td>

                <td class="text-center align-middle">
                    <figure class="avatar mb-0">
                        <img src="{{ $seller->user?->image ? asset('images/users/small/'.$seller->user->image) : asset('images/users/default-avatar.png') }}"
                             class="rounded-circle"
                             style="width: 40px; height: 40px; object-fit: cover;"
                             alt="{{ $seller->user?->name ?? 'کاربر سیستم' }}">
                    </figure>
                </td>

                <td class="text-center align-middle font-weight-bold">
                    {{ $seller->user?->name ?? 'بدون نام تعریفی' }}
                    @if($seller->last_name) <span class="text-muted">| {{ $seller->last_name }}</span> @endif
                </td>

                <td class="text-center align-middle text-muted">{{ $seller->user?->user_name ?? '--' }}</td>
                <td class="text-center align-middle text-left" dir="ltr">{{ $seller->user?->mobile ?? '--' }}</td>
                <td class="text-center align-middle text-info font-weight-bold">{{ $seller->brand_name ?? '--' }}</td>
                <td class="text-center align-middle font-numeric">{{ $seller->national_code ?? '--' }}</td>

                <td class="text-center align-middle">
                    {{-- ۱. تغییر وضعیت حساب فروشنده --}}
                    <div wire:click="changeStatus({{ $seller->id }})" style="cursor: pointer;" class="status-interactive-wrapper mb-2" title="جهت تغییر وضعیت کلیک کنید">
                        @if($seller->status === \App\Enums\SellerStatus::Active->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i>
                                <span>فعال</span>
                            </div>
                        @elseif($seller->status === \App\Enums\SellerStatus::Pending->value)
                            <div class="modern-status-btn waiting">
                                <div class="status-pulse"></div>
                                <i class="ti-time mr-1"></i>
                                <span>در حال بررسی</span>
                            </div>
                        @elseif($seller->status === \App\Enums\SellerStatus::Rejected->value)
                            <div class="modern-status-btn inactive">
                                <i class="ti-power-off mr-1"></i>
                                <span>غیرفعال</span>
                            </div>
                        @elseif($seller->status === \App\Enums\SellerStatus::Suspended->value)
                            <div class="modern-status-btn banned">
                                <i class="ti-na mr-1"></i>
                                <span>غیر مجاز</span>
                            </div>
                        @endif
                    </div>

                    {{-- ۲. وضعیت تاییدیه حساب بانکی جهت تسویه‌حساب (منطق ۳۰ روزه) --}}
                    <div>
                        @if($seller->bank_verified)
                            <span class="badge badge-success-light font-weight-normal px-2 py-1" style="font-size: 11px; border: 1px solid #28a74533;" title="اطلاعات بانکی تایید شده است">
                                <i class="ti-credit-card mr-1 text-success"></i> بانک: تایید شده
                            </span>
                        @else
                            <span class="badge badge-danger-light font-weight-normal px-2 py-1" style="font-size: 11px; border: 1px solid #dc354533;" title="اطلاعات بانکی تایید نشده است">
                                <i class="ti-credit-card mr-1 text-danger"></i> بانک: تایید نشده
                            </span>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle">
                    <a class="btn btn-sm btn-outline-info" href="{{ route('admin.seller.detail', $seller->id) }}">
                        <i class="ti-id-badge mr-1"></i> جزئیات فروشنده
                    </a>
                </td>
                <td class="text-center align-middle text-muted">
                    {{ \Hekmatinasser\Verta\Verta::instance($seller->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                        <p class="text-muted">فروشنده‌ای با عبارت <strong class="text-danger">"{{ $search }}"</strong> در سیستم پیدا نشد.</p>
                        @if($search)
                            <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="ti-eraser mr-1"></i> پاکسازی جستجو
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin: 40px !important;" class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{ $sellers->appends(Request::except('page'))->links() }}
    </div>
</div>
