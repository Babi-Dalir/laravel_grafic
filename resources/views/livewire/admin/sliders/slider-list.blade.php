<div class="table overflow-auto" tabindex="8">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">عنوان جستجو</label>
        <div class="col-sm-10">
            <input type="text" class="form-control text-left" dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="جستجو در لینک اسلایدرها...">
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">عکس</th>
            <th class="text-center align-middle text-primary">وضعیت</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sliders as $index => $slider)
            <tr>
                <td class="text-center align-middle">{{ $sliders->firstItem() + $index }}</td>
                <td class="text-center align-middle">
                    <figure class="m-0">
                        <img style="height: 50px; width: 120px; object-fit: cover;"
                             src="{{ url('images/sliders/small/' . $slider->image) }}"
                             alt="slider image" class="img-thumbnail">
                    </figure>
                </td>
                <td class="text-center align-middle">
                    <div wire:click="changeStatus({{ $slider->id }})" style="cursor: pointer;" class="status-interactive-wrapper">
                        @if($slider->status === \App\Enums\SliderStatus::Active)
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
                    <a class="btn btn-outline-info" href="{{ route('sliders.edit', $slider->id) }}">
                        ویرایش
                    </a>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger"
                            wire:click="$dispatch('triggerDeleteSlider', { id: {{ $slider->id }} })">
                        حذف
                    </button>
                </td>
                <td class="text-center align-middle">
                    {{ \Hekmatinasser\Verta\Verta::instance($slider->created_at)->format('%d %B، %Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center align-middle text-muted py-4">هیچ اسلایدری یافت نشد.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
        {{ $sliders->links() }}
    </div>
</div>

@section('scripts')
    <script>
        Livewire.on('triggerDeleteSlider', (event) => {
            const sliderId = event.id;

            Swal.fire({
                title: "آیا از حذف اسلایدر مطمئن هستید؟",
                text: "با حذف اسلایدر، فایل فیزیکی آن نیز از روی سرور پاک خواهد شد.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "بله، حذف شود",
                cancelButtonText: "خیر",
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('destroy_slider', { id: sliderId });
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
