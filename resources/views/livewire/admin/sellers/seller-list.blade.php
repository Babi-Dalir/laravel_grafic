<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (نام، ایمیل، موبایل)</label>
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
            <th class="text-center align-middle text-primary">عکس</th>
            <th class="text-center align-middle text-primary">نام و نام خانوادگی</th>
            <th class="text-center align-middle text-primary">نام کاربری </th>
            <th class="text-center align-middle text-primary">موبایل</th>
            <th class="text-center align-middle text-primary">نام برند</th>
            <th class="text-center align-middle text-primary">کد ملی</th>
            <th class="text-center align-middle text-primary"> وضعیت</th>
            <th class="text-center align-middle text-primary"> جزئیات فروشنده</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sellers as $index => $seller)
            <tr>
                <td class="text-center align-middle">{{$sellers->firstItem()+$index}}</td>

                @php
                    $image = $seller->user->image
                        ? asset('images/users/small/'.$seller->user->image)
                        : asset('images/users/default-avatar.png');
                @endphp

                <td class="text-center align-middle">
                    <figure class="avatar avatar">
                        <img src="{{$image}}" class="rounded-circle" alt="{{$seller->user->name}}">
                    </figure>
                </td>
                <td class="text-center align-middle">{{$seller->user->name}} -- {{$seller->last_name}}</td>
                <td class="text-center align-middle">{{$seller->user->user_name ?? '--'}}</td>
                <td class="text-center align-middle">{{$seller->user->mobile}}</td>
                <td class="text-center align-middle">{{$seller->brand_name ?? '--'}}</td>
                <td class="text-center align-middle">{{$seller->national_code ?? '--'}}</td>
                <td class="text-center align-middle">
                    <div wire:click="changeStatus({{$seller->id}})" class="status-interactive-wrapper">
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
                                <span> در حال بررسی</span>
                            </div>
                        @elseif($seller->status === \App\Enums\SellerStatus::Rejected->value)
                            <div class="modern-status-btn inactive">
                                <i class="ti-power-off mr-1"></i>
                                <span>غیرفعال</span>
                            </div>
                        @elseif($seller->status === \App\Enums\SellerStatus::Suspended->value)
                            <div class="modern-status-btn banned">
                                <i class="ti-na ml-1"></i>
                                <span>غیر مجاز</span>
                            </div>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle">
                    <a class="btn btn-outline-info" href="{{route('admin.seller.detail',$seller->id)}}">
                        جزئیات فروشنده
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($seller->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        {{-- یک SVG ساده و شیک برای حالت جستجو --}}
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </small>

                            <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                            <p class="text-muted">کاربری با عبارت <strong class="text-danger">"{{ $search }}"</strong>
                                در سیستم ثبت نشده است.</p>

                            @if($search)
                                <button wire:click="$set('search', '')" class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="ti-eraser m-r-5"></i> پاکسازی جستجو
                                </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
    </table>
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$sellers->appends(Request::except('page'))->links()}}
    </div>
</div>

