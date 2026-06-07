<div>

    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <h5 class="mb-4">
        آپلود فایل‌های محصول:
        <span class="text-primary">{{ $product->name }}</span>
    </h5>

    {{-- فرم آپلود --}}
    <form wire:submit.prevent="upload"
          class="border rounded p-4 mb-4">

        <div class="form-group mb-3">

            <label class="font-weight-bold">
                عنوان فایل (اختیاری)
            </label>

            <input
                type="text"
                wire:model.defer="title"
                class="form-control"
                placeholder="مثلا فایل اصلی PSD یا لایسنس">
        </div>

        <div class="form-group mb-3">

            <label class="font-weight-bold">
                انتخاب فایل
            </label>

            <input
                type="file"
                wire:model="file"
                class="form-control">

            <small class="text-muted">
                فرمت‌های مجاز:
                zip, rar, 7z, psd, ai, eps, pdf, ttf, otf
            </small>

            @error('file')
            <div class="text-danger mt-2">
                {{ $message }}
            </div>
            @enderror

            <div wire:loading wire:target="file" class="mt-2">
                <span class="spinner-border spinner-border-sm"></span>
                در حال آپلود فایل...
            </div>

        </div>

        <button
            type="submit"
            class="btn btn-success"
            wire:loading.attr="disabled">

            <span wire:loading.remove wire:target="upload">
                <i class="ti-upload"></i>
                آپلود فایل
            </span>

            <span wire:loading wire:target="upload">
                <i class="fa fa-spinner fa-spin"></i>
                در حال پردازش...
            </span>

        </button>

    </form>

    {{-- لیست فایل‌ها --}}
    <div class="card">

        <div class="card-header">
            <h6 class="mb-0">
                فایل‌های آپلود شده
                ({{ $files->count() }})
            </h6>
        </div>

        <div class="card-body p-0">

            <table class="table table-striped table-hover">

                <thead class="thead-light">
                <tr>
                    <th class="text-center">ردیف</th>
                    <th class="text-center">نام فایل</th>
                    <th class="text-center">نوع</th>
                    <th class="text-center">حجم</th>
                    <th class="text-center">فایل اصلی</th>
                    <th class="text-center">حذف</th>
                </tr>
                </thead>

                <tbody>

                @forelse($files as $index => $file)

                    <tr>

                        <td class="text-center align-middle">
                            {{ $index + 1 }}
                        </td>

                        <td class="align-middle">

                            <strong>
                                {{ $file->original_name }}
                            </strong>

                            @if($file->title)
                                <br>
                                <small class="text-muted">
                                    {{ $file->title }}
                                </small>
                            @endif

                        </td>

                        <td class="text-center align-middle">
                            {{ strtoupper($file->extension) }}
                        </td>

                        <td class="text-center align-middle">
                            {{ $file->human_size }}
                        </td>

                        <td class="text-center align-middle">

                            @if($file->is_default)

                                <span class="badge badge-success">
                        فایل اصلی
                    </span>

                            @else

                                <button
                                    wire:click="setDefault({{ $file->id }})"
                                    class="btn btn-outline-primary btn-sm">

                                    انتخاب

                                </button>

                            @endif

                        </td>

                        <td class="text-center align-middle">

                            <button
                                class="btn btn-outline-danger btn-sm"
                                wire:click="$dispatch('deleteProductFile',{
                        file_id: {{ $file->id }}
                    })">

                                حذف

                            </button>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center text-muted">

                            هنوز فایلی آپلود نشده است

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- مرحله بعد --}}
    @if($files->count())

        <div class="text-center mt-4">

            <a
                href="#"
                class="btn btn-primary btn-lg">

                ثبت محصول و ارسال برای بررسی
            </a>

        </div>

    @endif

</div>
@section('scripts')

    <script>
        Livewire.on('deleteProductFile', (event) => {

            Swal.fire({
                title: "آیا از حذف فایل مطمئن هستید؟",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "بله",
                cancelButtonText: "خیر",
            }).then((result) => {

                if (result.isConfirmed) {

                    Livewire.dispatch(
                        'destroy_product_file',
                        {
                            fileId: event.file_id
                        }
                    );

                    Swal.fire({
                        title: "فایل حذف شد",
                        icon: "success"
                    });
                }
            });
        });
    </script>

@endsection
