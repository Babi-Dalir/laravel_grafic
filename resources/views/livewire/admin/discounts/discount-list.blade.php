<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (کد)</label>
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
            <th class="text-center align-middle text-primary">کد تخفیف</th>
            <th class="text-center align-middle text-primary">میزان تخفیف</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary"> تاریخ انقضا</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($discounts as $index=>$discount)
            <tr>
                <td class="text-center align-middle">{{$discounts->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$discount->code}}</td>
                <td class="text-center align-middle">{{number_format($discount->discount)}} تومان </td>

                <td class="text-center align-middle">
                    <div class="status-interactive-wrapper" wire:click="changeStatus({{$discount->id}})">
                        @if($discount->status === \App\Enums\DiscountStatus::Active->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i>
                                <span>فعال</span>
                            </div>
                        @elseif($discount->status === \App\Enums\DiscountStatus::InActive->value)
                            <div class="modern-status-btn inactive">
                                <i class="ti-power-off mr-1"></i>
                                <span>غیرفعال</span>
                            </div>
                        @endif

                    </div>
                </td>
                <td class="text-center align-middle">
                    {{\Hekmatinasser\Verta\Verta::instance($discount->expiration_date)->format('%d %B، %Y')}}
                    @if(\Illuminate\Support\Carbon::now()->gt($discount->expiration_date))
                        <br><span class="badge badge-danger">منقضی شده</span>
                    @endif
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteDiscount',{'id':{{$discount->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($discount->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        {{-- یک SVG ساده و شیک برای حالت جستجو --}}
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </small>

                            <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                            <p class="text-muted">کدی با عبارت <strong class="text-danger">"{{ $search }}"</strong>
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
        {{$discounts->appends(Request::except('page'))->links()}}
    </div>
</div>

@section('scripts')
    <script>
        Livewire.on('deleteDiscount', (event) => {
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
                    Livewire.dispatch('destroy_discount',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


