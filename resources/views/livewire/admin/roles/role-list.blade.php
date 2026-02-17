<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو (عنوان نقش)</label>
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
            <th class="text-center align-middle text-primary">عنوان نقش</th>
            <th class="text-center align-middle text-primary">مجوزها</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($roles as $index=>$role)
            <tr>
                <td class="text-center align-middle">{{$roles->firstItem()+$index}}</td>
                <td class="text-center align-middle">{{$role->name}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-dark" href="{{route('create.role.permission',$role->id)}}">
                        مجوزها
                    </a>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-info" href="{{route('roles.edit',$role->id)}}">
                        ویرایش
                    </a>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteRole',{'id':{{$role->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($role->created_at)->format('%d%B، %Y')}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-5" style="background-color: #f9f9f966;">
                    <div class="empty-state">
                        {{-- یک SVG ساده و شیک برای حالت جستجو --}}
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </small>

                            <h5 class="text-dark" style="font-weight: 600;">نتیجه‌ای یافت نشد!</h5>
                            <p class="text-muted">نقشی با عبارت <strong class="text-danger">"{{ $search }}"</strong> در سیستم ثبت نشده است.</p>

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
        {{$roles->appends(Request::except('page'))->links()}}
    </div>
</div>
@section('scripts')
    <script>
        Livewire.on('deleteRole', (event) => {
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
                    Livewire.dispatch('destroy_role',{id : event.id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection


