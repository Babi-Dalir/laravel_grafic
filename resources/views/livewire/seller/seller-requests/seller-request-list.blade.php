<div class="table overflow-auto" tabindex="8">
    <div class="form-group row align-items-center">
        <label class="col-sm-2 col-form-label font-weight-bold">جستجو (نام، ایمیل، موبایل):</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="عبارت مورد نظر را جهت جستجو تایپ کنید...">

            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    {{-- نمایش پیام‌های موفقیت فلاش سیستم --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <table class="table table-striped table-hover mt-3">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">کاربر</th>
            <th class="text-center align-middle text-primary">برند</th>
            <th class="text-center align-middle text-primary">موبایل</th>
            <th class="text-center align-middle text-primary">نمونه کار</th>
            <th class="text-center align-middle text-primary">رزومه</th>
            <th class="text-center align-middle text-primary">تلگرام</th>
            <th class="text-center align-middle text-primary">اینستاگرام</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary">عملیات</th>
            <th class="text-center align-middle text-primary">تاریخ ثبت</th>
        </tr>
        </thead>

        <tbody>
        @forelse($seller_requests as $index => $request)
            <tr>
                <td class="text-center align-middle">
                    {{ $seller_requests->firstItem() + $index }}
                </td>

                <td class="text-center align-middle">
                    <div>
                        <strong>{{ $request->user?->name ?? 'کاربر حذف شده' }}</strong>
                        <br>
                        <small class="text-muted">{{ $request->user?->email }}</small>
                    </div>
                </td>

                <td class="text-center align-middle">
                    {{ $request->brand_name ?? '---' }}
                </td>

                <td class="text-center align-middle">
                    {{ $request->user?->mobile ?? '---' }}
                </td>

                <td class="text-center align-middle">
                    @if($request->portfolio)
                        <a href="{{ $request->portfolio }}" target="_blank" class="btn btn-outline-info btn-sm">
                            مشاهده
                        </a>
                    @else
                        ---
                    @endif
                </td>

                <td class="text-center align-middle">
                    @if(!empty($request->resume))
                        <a class="btn btn-sm btn-outline-info" href="{{ route('download.resume', $request->id) }}">
                            <i class="ti-download"></i> دانلود
                        </a>
                    @else
                        <span class="text-muted">---</span>
                    @endif
                </td>

                {{-- 🟢 پچ امنیتی: جلوگیری از خطای کرش صفحه در صورت خالی بودن پروفایل کاربر با استفاده از ?-> --}}
                <td class="text-center align-middle">
                    {{ $request->user?->userProfile?->telegram ?? '---' }}
                </td>

                <td class="text-center align-middle">
                    {{ $request->user?->userProfile?->instagram ?? '---' }}
                </td>

                <td class="text-center align-middle">
                    {{-- 🟢 فعال‌سازی مجدد کلیک چرخشی وضعیت با استایل نشانگر ماوس --}}
                    <div class="status-interactive-wrapper"
                         wire:click="changeStatus({{ $request->id }})"
                         style="cursor: pointer;"
                         title="جهت تغییر سریع وضعیت کلیک کنید">

                        @if($request->status === \App\Enums\SellerRequestStatus::Pending->value)
                            <div class="modern-status-btn waiting">
                                <div class="status-pulse"></div>
                                <i class="ti-time mr-1"></i> <span>در حال بررسی</span>
                            </div>
                        @elseif($request->status === \App\Enums\SellerRequestStatus::Approved->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i> <span>تایید شده</span>
                            </div>
                        @elseif($request->status === \App\Enums\SellerRequestStatus::Rejected->value)
                            <div class="modern-status-btn inactive" title="دلیل رد: {{ $request->admin_note }}">
                                <i class="ti-close mr-1"></i> <span>رد شده</span>
                            </div>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle">
                    @if($request->status === \App\Enums\SellerRequestStatus::Pending->value)
                        <div class="d-flex gap-1 justify-content-center">
                            <button wire:click="approveRequest({{ $request->id }})" class="btn btn-success btn-sm">
                                تایید
                            </button>
                            <button class="btn btn-danger btn-sm"
                                    wire:click="$set('sellerRequestId', {{ $request->id }})"
                                    data-toggle="modal"
                                    data-target="#rejectModal">
                                رد
                            </button>
                        </div>
                    @else
                        <span class="text-muted text-sm">بررسی شده</span>
                    @endif
                </td>

                <td class="text-center align-middle">
                    {{ verta($request->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        <h5 class="text-dark font-weight-bold">درخواستی یافت نشد</h5>
                        <p class="text-muted">هیچ درخواست فروشندگی در سیستم ثبت نشده است.</p>
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

    {{-- Modal رد درخواست --}}
    <div wire:ignore.self class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">دلیل رد درخواست فروشندگی</h5>
                    <button type="button" class="close" data-dismiss="alert" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <textarea wire:model="admin_note" rows="5"
                              class="form-control @error('admin_note') is-invalid @enderror"
                              placeholder="لطفاً دلیل صریح رد درخواست را وارد کنید..."></textarea>
                    @error('admin_note')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
                    <button wire:click="rejectRequest" class="btn btn-danger">ثبت و رد درخواست</button>
                </div>
            </div>
        </div>
    </div>

    <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
        {{ $seller_requests->appends(Request::except('page'))->links() }}
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('closeRejectModal', () => {
            $('#rejectModal').modal('hide');
            // در بوت‌استرپ ۴ گاهی پس‌زمینه مودال باقی می‌ماند که دستور زیر آن را کاملاً پاکسازی می‌کند
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });
    });
</script>
