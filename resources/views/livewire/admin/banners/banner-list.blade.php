<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">جستجو بر اساس نوع بنر</label>
        <div class="col-sm-10">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="تایپ کنید و کمی منتظر بمانید...">
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">تصویر</th>
            <th class="text-center align-middle text-primary">نوع بنر</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($banners as $index => $banner)
            <tr>
                <td class="text-center align-middle">{{ $banners->firstItem() + $index }}</td>
                <td class="text-center align-middle">
                    <img src="{{ asset('images/banners/big/' . $banner->image) }}" alt="Banner" style="width: 80px; height: auto;" class="img-thumbnail">
                </td>
                <td class="text-center align-middle">{{ $banner->type }}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-info" href="{{ route('banners.edit', $banner->id) }}">
                        ویرایش
                    </a>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger"
                            wire:click="$dispatch('triggerDelete', { id: {{ $banner->id }} })">
                        حذف
                    </button>
                </td>
                <td class="text-center align-middle">
                    {{ \Hekmatinasser\Verta\Verta::instance($banner->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center align-middle text-muted py-4">هیچ بنری یافت نشد.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
        {{ $banners->links() }}
    </div>
</div>

@section('scripts')
    <script>
        // مپ کردن رویداد با سینتکس بومی و تمیز لایووایر ۳
        Livewire.on('triggerDelete', (event) => {
            // استخراج ایمن آیدی از آبجکت دیسپچ شده
            const bannerId = event.id;

            Swal.fire({
                title: "آیا از حذف مطمئن هستید؟",
                text: "این عملیات غیرقابل بازگشت است و کش سیستم نیز پاک خواهد شد.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "بله، حذف شود",
                cancelButtonText: "خیر، لغو کن",
            }).then((result) => {
                if (result.isConfirmed) {
                    // شلیک رویداد نهایی به متد On کامپوننت
                    Livewire.dispatch('destroy_banner', { id: bannerId });

                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
    </script>
@endsection
