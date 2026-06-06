<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (نام، ایمیل، موبایل)</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="تایپ کنید...">

            <div wire:loading class="spinner-border spinner-border-sm text-primary m-r-10"></div>
        </div>
    </div>

    <table class="table table-striped table-hover">
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
                    {{$seller_requests->firstItem() + $index}}
                </td>

                <td class="text-center align-middle">
                    <div>
                        <strong>{{$request->user->name}}</strong>
                        <br>
                        <small class="text-muted">
                            {{$request->user->email}}
                        </small>
                    </div>
                </td>

                <td class="text-center align-middle">
                    {{$request->brand_name ?? '---'}}
                </td>

                <td class="text-center align-middle">
                    {{$request->user->mobile}}
                </td>

                <td class="text-center align-middle">
                    @if($request->portfolio)
                        <a href="{{$request->portfolio}}"
                           target="_blank"
                           class="btn btn-outline-info btn-sm">
                            مشاهده
                        </a>
                    @else
                        ---
                    @endif
                </td>

                <td class="text-center align-middle">
                    <a class="btn btn-outline-info" href="{{route('download.resume',$request->id)}}">
                        دانلود رزومه
                    </a>
                </td>
                <td class="text-center align-middle">
                    @if($request->user->userProfile->telegram)
                        {{$request->user->userProfile->telegram}}
                    @else
                        ---
                    @endif
                </td>
                <td class="text-center align-middle">
                    @if($request->user->userProfile->instagram)
                        {{$request->user->userProfile->instagram}}
                    @else
                        ---
                    @endif
                </td>

                <td class="text-center align-middle">
                    <div class="status-interactive-wrapper" wire:click="changeStatus({{$request->id}})">
                        @if($request->status === \App\Enums\SellerRequestStatus::Pending->value)
                            <div class="modern-status-btn waiting">
                                <div class="status-pulse"></div>
                                <i class="ti-time mr-1"></i>
                                <span>در حال بررسی</span>
                            </div>

                        @elseif($request->status === \App\Enums\SellerRequestStatus::Approved->value)
                            <div class="modern-status-btn active">
                                <div class="status-glow"></div>
                                <i class="ti-check-box mr-1"></i>
                                <span>تایید شده</span>
                            </div>

                        @elseif($request->status === \App\Enums\SellerRequestStatus::Rejected->value)
                            <div class="modern-status-btn inactive">
                                <i class="ti-close mr-1"></i>
                                <span>رد شده</span>
                            </div>
                    @endif

                </td>

                <td class="text-center">

                    @if($request->status === \App\Enums\SellerRequestStatus::Pending->value)

                        <button
                            wire:click="approveRequest({{ $request->id }})"
                            class="btn btn-success btn-sm">

                            تایید
                        </button>

                        <button
                            class="btn btn-danger btn-sm"
                            wire:click="$set('sellerRequestId', {{ $request->id }})"
                            data-toggle="modal"
                            data-target="#rejectModal">

                            رد
                        </button>

                    @endif

                </td>
                <td class="text-center align-middle">
                    {{\Hekmatinasser\Verta\Verta::instance($request->created_at)->format('%d %B، %Y')}}
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="10"
                    class="text-center py-5"
                    style="background-color: #f9f9f966;">

                    <div class="empty-state">

                        <svg width="80"
                             height="80"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="#d1d5db"
                             stroke-width="1.5"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>

                        <h5 class="text-dark" style="font-weight: 600;">
                            درخواستی یافت نشد
                        </h5>

                        <p class="text-muted">
                            هیچ درخواست فروشندگی ثبت نشده است.
                        </p>

                        @if($search)
                            <button wire:click="$set('search', '')"
                                    class="btn btn-outline-primary btn-sm mt-2">

                                <i class="ti-eraser m-r-5"></i>
                                پاکسازی جستجو

                            </button>
                        @endif

                    </div>

                </td>
            </tr>

        @endforelse

        </tbody>
    </table>
    {{-- Modal --}}
    <div
        wire:ignore.self
        class="modal fade"
        id="rejectModal"
        tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        دلیل رد درخواست
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                <textarea
                    wire:model="admin_note"
                    rows="5"
                    class="form-control"
                    placeholder="دلیل رد درخواست را وارد کنید...">
                </textarea>

                </div>

                <div class="modal-footer">

                    <button
                        wire:click="rejectRequest"
                        class="btn btn-danger">

                        ثبت و رد درخواست

                    </button>

                </div>

            </div>

        </div>

    </div>

    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">

        {{$seller_requests->appends(Request::except('page'))->links()}}

    </div>
</div>
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('closeRejectModal', () => {

            console.log('EVENT FIRED');

            $('#rejectModal').modal('hide');

        });

    });
</script>
