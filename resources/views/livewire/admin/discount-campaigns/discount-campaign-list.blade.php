<div class="table overflow-auto" tabindex="8">
    {{-- بخش جستجو --}}
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (نام کمپین)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search" placeholder="نام کمپین را تایپ کنید...">
            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">نام کمپین</th>
            <th class="text-center align-middle text-primary">نوع</th>
            <th class="text-center align-middle text-primary">درصد تخفیف</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary">تاریخ انقضا</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
        </tr>
        </thead>
        <tbody>
        @forelse($campaigns as $index => $campaign)
            <tr>
                <td class="text-center align-middle">{{ $campaigns->firstItem() + $index }}</td>
                <td class="text-center align-middle">{{ $campaign->name }}</td>

                <td class="text-center align-middle">
                    @if($campaign->type === \App\Enums\DiscountCampaignType::Product->value)
                        <span class="badge badge-info-border">محصولی</span>
                    @elseif($campaign->type === \App\Enums\DiscountCampaignType::Category->value)
                        <span class="badge badge-warning-border">دسته‌بندی</span>
                    @else
                        <span class="badge badge-primary-border">کل سایت</span>
                    @endif
                </td>

                <td class="text-center align-middle text-success font-weight-bold">{{ $campaign->percent }}%</td>

                <td class="text-center align-middle">
                    <div class="status-interactive-wrapper" style="cursor: pointer"
                         wire:click="changeStatus({{ $campaign->id }})">
                        {{-- 🟢 مقایسه مستقیم Enum با Enum --}}
                        @if($campaign->status === \App\Enums\DiscountCampaignStatus::Active)
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
                    @if($campaign->expires_at)
                        {{ \Hekmatinasser\Verta\Verta::instance($campaign->expires_at)->format('%d %B، %Y') }}

                        @if(now()->greaterThan($campaign->expires_at))
                            <br><span class="badge badge-danger small">منقضی شده</span>
                        @endif
                    @else
                        <span class="text-muted">بدون انقضا</span>
                    @endif
                </td>

                <td class="text-center align-middle">
                    <a href="{{ route('discount_campaigns.edit', $campaign->id) }}" class="btn btn-outline-info btn-sm">
                        <i class="ti-pencil"></i> ویرایش
                    </a>
                </td>

                <td class="text-center align-middle">
                    <button class="btn btn-outline-danger btn-sm"
                            wire:click="$dispatch('trigger_delete_campaign', { id: {{ $campaign->id }} })">
                        حذف
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="empty-state">
                        <h5 class="text-dark" style="font-weight: 600;">کمپینی یافت نشد!</h5>
                        @if($search)
                            <p class="text-muted">عبارت "<strong class="text-danger">{{ $search }}</strong>" نتیجه‌ای نداشت.</p>
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

    <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
        {{ $campaigns->appends(Request::except('page'))->links() }}
    </div>
</div>

@section('scripts')
    <script>
        Livewire.on('trigger_delete_campaign', (event) => {
            Swal.fire({
                title: "آیا از حذف این کمپین مطمئن هستید؟",
                text: "با حذف کمپین، تمام اهداف مرتبط با آن نیز حذف خواهند شد.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "بله، حذف شود",
                cancelButtonText: "انصراف",
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('destroy_discount_campaign', {id: event.id});
                    Swal.fire({
                        title: "حذف شد!",
                        icon: "success"
                    });
                }
            });
        });

        Livewire.on('showToastCampaignError', (event) => {
            Swal.fire({
                title: "خطا!",
                text: event.message,
                icon: "error",
                confirmButtonText: "متوجه شدم"
            });
        });
    </script>
@endsection
