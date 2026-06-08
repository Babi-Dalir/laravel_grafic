<div>

    {{-- پیام موفقیت --}}
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    {{-- پیام خطای کلی --}}
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- خطاهای review (از addError) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h5 class="mb-4">
        آپلود فایل‌های محصول:
        <span class="text-primary">{{ $product->name }}</span>
    </h5>

    {{-- فرم آپلود --}}
    <form wire:submit.prevent="uploadFile"
          class="border rounded p-4 mb-4">

        <div class="form-group mb-3">

            <label class="font-weight-bold">عنوان فایل (اختیاری)</label>

            <input type="text"
                   wire:model.defer="title"
                   class="form-control"
                   placeholder="مثلا فایل اصلی PSD یا لایسنس">
        </div>

        <div class="form-group mb-3">

            <label class="font-weight-bold">انتخاب فایل</label>

            <input type="file"
                   wire:model="file"
                   class="form-control">

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

        <button type="submit"
                class="btn btn-success"
                wire:loading.attr="disabled">

            آپلود فایل
        </button>

    </form>

    {{-- لیست فایل‌ها --}}
    <div class="card">

        <div class="card-header">
            <h6 class="mb-0">
                فایل‌های آپلود شده ({{ $files->count() }})
            </h6>
        </div>

        <div class="card-body p-0">

            <table class="table table-striped table-hover">

                <thead class="thead-light">
                <tr>
                    <th>ردیف</th>
                    <th>نام فایل</th>
                    <th>نوع</th>
                    <th>حجم</th>
                    <th>اصلی</th>
                    <th>حذف</th>
                </tr>
                </thead>

                <tbody>

                @forelse($files as $index => $file)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            <strong>{{ $file->original_name }}</strong>

                            @if($file->title)
                                <br>
                                <small class="text-muted">{{ $file->title }}</small>
                            @endif
                        </td>

                        <td>{{ strtoupper($file->extension) }}</td>
                        <td>{{ $file->human_size }}</td>

                        <td>
                            @if($file->is_default)
                                <span class="badge badge-success">اصلی</span>
                            @else
                                <button wire:click="setDefault({{ $file->id }})"
                                        class="btn btn-sm btn-outline-primary">
                                    انتخاب
                                </button>
                            @endif
                        </td>

                        <td>
                            <button class="btn btn-sm btn-danger"
                                    wire:click="$dispatch('deleteProductFile',{ file_id: {{ $file->id }} })">
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

    {{-- دکمه ارسال برای بررسی + خطاهای مرحله --}}
    @if($files->count())

        <div class="mt-4">

            <button wire:click="submitForReview"
                    class="btn btn-success btn-lg">
                ثبت محصول و ارسال برای بررسی
            </button>

            {{-- 👇 نمایش خطاهای بررسی محصول --}}
            @if (session()->has('review_errors'))

                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach (session('review_errors') as $field => $message)
                            <li>
                                <strong>{{ $field }}:</strong> {{ $message }}
                            </li>
                        @endforeach
                    </ul>
                </div>

            @endif

        </div>

    @endif

</div>
