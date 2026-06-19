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

    {{-- خطاهای سیستم آپلود و بررسی --}}
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

    {{-- 🌟 اضافه شدن wire:ignore برای جلوگیری از تداخل باز-رندر لایووایر با دستکاری‌های جاوااسکریپت --}}
    <div class="border rounded p-4 mb-4 bg-light" wire:ignore>
        <div class="form-group mb-3">
            <label class="font-weight-bold">عنوان فایل (اختیاری)</label>
            <input type="text"
                   wire:model.blur="title"
                   id="file-title"
                   class="form-control"
                   placeholder="مثلا فایل اصلی PSD یا لایسنس">
        </div>

        <div class="form-group mb-3">
            <label class="font-weight-bold">انتخاب فایل محصول</label>

            <div id="upload-container" class="p-3 text-center border-dashed rounded bg-white" style="border: 2px dashed #ccc;">
                <input type="file" id="file-uploader" class="form-control-file d-none">
                <button type="button" class="btn btn-outline-primary" id="browse-btn">انتخاب فایل محصول (حجیم)</button>
                <div class="mt-2 text-muted small" id="selected-file-info">هیچ فایلی انتخاب نشده است</div>
            </div>

            <div class="mt-2">
                <small class="text-muted d-block mb-2">فرمت‌های مجاز:</small>
                @foreach(config('uploads.allowed_extensions', ['zip', 'psd', 'rar']) as $extension)
                    <span class="badge badge-secondary">{{ strtoupper($extension) }}</span>
                @endforeach
                <small class="text-muted d-block mt-2">فایل‌های ZIP رمزگذاری‌شده و تو در تو پذیرفته نمی‌شوند.</small>
            </div>

            @error('file')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror

            {{-- پروگرس بار واقعی برای فایلهای حجیم گرافیکی --}}
            <div id="progress-wrapper" class="mt-3 d-none">
                <div class="progress" style="height: 20px;">
                    <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                         role="progressbar" style="width: 0%;">0%</div>
                </div>
                <small class="text-muted mt-1 d-block" id="upload-status-text">در حال آماده‌سازی و ارسال تکه‌ها...</small>
            </div>
        </div>

        <button type="button" id="start-upload-btn" class="btn btn-success" disabled>
            شروع آپلود فایل حجیم
        </button>
    </div>

    {{-- لیست فایل‌ها --}}
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">فایل‌های آپلود شده ({{ $files->count() }})</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light">
                <tr>
                    <th>ردیف</th>
                    <th>نام فایل</th>
                    <th>نوع</th>
                    <th>حجم</th>
                    <th>اصلی</th>
                    @role('مدیر') <th>دانلود</th> @endrole
                    <th>حذف</th>
                </tr>
                </thead>
                <tbody>
                @forelse($files as $index => $file)
                    <tr>
                        <td>{{ $files->firstItem()+$index }}</td>
                        <td>
                            <strong>{{ $file->original_name }}</strong>
                            @if($file->title)
                                <br><small class="text-muted">{{ $file->title }}</small>
                            @endif
                        </td>
                        <td>{{ strtoupper($file->extension) }}</td>
                        <td>{{ $file->human_size ?? number_format($file->size / 1048576, 2) . ' MB' }}</td>
                        <td>
                            @if($file->is_default)
                                <span class="badge badge-success">اصلی</span>
                            @else
                                <button wire:click="setDefault({{ $file->id }})" class="btn btn-sm btn-outline-primary">انتخاب</button>
                            @endif
                        </td>
                        @role('مدیر')
                        <td>
                            <a href="{{ route('product.files.download', $file) }}" class="btn btn-sm btn-success">دانلود</a>
                        </td>
                        @endrole
                        <td>
                            <button class="btn btn-sm btn-danger" wire:click="deleteFile({{ $file->id }})">حذف</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">هنوز فایلی برای این محصول آپلود نشده است</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- دکمه ارسال برای بررسی --}}
    @if($files->count())
        <div class="mt-4">
            <button wire:click="submitForReview" class="btn btn-success btn-lg">ثبت محصول </button>
            @if (session()->has('review_errors'))
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach (session('review_errors') as $field => $message)
                            <li><strong>{{ $field }}:</strong> {{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <div style="margin: 40px !important;" class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$files->appends(Request::except('page'))->links()}}
    </div>

    {{-- 🔥 بخش جاوااسکریپت اصلاح‌شده --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Resumable === 'undefined') {
                console.error('خطا: کتابخانه Resumable.js بارگذاری نشده است. مسیر فایل فیزیکی را بررسی کنید.');
                return;
            }

            const browseBtn = document.getElementById('browse-btn');
            const fileInput = document.getElementById('file-uploader');
            const fileInfo = document.getElementById('selected-file-info');
            const startBtn = document.getElementById('start-upload-btn');
            const progressWrapper = document.getElementById('progress-wrapper');
            const progressBar = document.getElementById('upload-progress-bar');
            const statusText = document.getElementById('upload-status-text');

            const r = new Resumable({
                target: '#',
                chunkSize: 5 * 1024 * 1024,
                simultaneousUploads: 1,
                testChunks: false,
                throttleProgressCallbacks: 0.1
            });

            r.assignBrowse(browseBtn);
            r.assignBrowse(fileInput);

            r.on('fileAdded', function(file) {
                fileInfo.innerHTML = `<strong>فایل انتخاب شده:</strong> ${file.fileName} (${(file.size / 1048576).toFixed(2)} MB)`;
                startBtn.disabled = false;
            });

            // 🌟 اضافه شدن پارامتر e و متد e.preventDefault() برای کنترل دقیق رفتار دکمه
            startBtn.addEventListener('click', function(e) {
                e.preventDefault();

                if (r.files.length > 0) {
                    startBtn.disabled = true;
                    progressWrapper.classList.remove('d-none');

                    processAndSendChunk(r.files[0], 0);
                }
            });

            function processAndSendChunk(resumableFile, index) {
                const totalChunks = resumableFile.chunks.length;
                const chunk = resumableFile.chunks[index];

                statusText.innerText = `در حال آماده‌سازی و ارسال تکه ${index + 1} از ${totalChunks}...`;

                const nativeFile = resumableFile.file;
                const blob = nativeFile.slice(chunk.startByte, chunk.endByte);

                const fileReader = new FileReader();
                fileReader.onload = function(e) {
                    const arrayBuffer = e.target.result;
                    const bytes = new Uint8Array(arrayBuffer);

                    let binaryString = '';
                    const len = bytes.byteLength;
                    for (let i = 0; i < len; i++) {
                        binaryString += String.fromCharCode(bytes[i]);
                    }
                    const base64Data = btoa(binaryString);

                    @this.call('handleChunkUpload',
                        resumableFile.uniqueIdentifier,
                        index,
                        totalChunks,
                        resumableFile.fileName,
                        base64Data
                    ).then(response => {
                        if (response && response.status === 'error') {
                            // 🌟 تغییر ظاهر پروگرس بار به وضعیت خطا (قرمز رنگ شدن)
                            progressBar.classList.remove('bg-success', 'progress-bar-animated');
                            progressBar.classList.add('bg-danger');

                            statusText.innerHTML = `<span class="text-danger font-weight-bold">❌ ${response.message}</span>`;
                            startBtn.disabled = false;
                            return;
                        }

                        const progressPercent = Math.round(((index + 1) / totalChunks) * 100);
                        progressBar.style.width = `${progressPercent}%`;
                        progressBar.innerText = `${progressPercent}%`;

                        if (index + 1 < totalChunks) {
                            processAndSendChunk(resumableFile, index + 1);
                        } else {
                            statusText.innerText = 'ادغام تکه‌ها و بررسی امنیت فایل نهایی زیپ...';
                            setTimeout(() => {
                                progressWrapper.classList.add('d-none');
                                r.cancel();
                                fileInfo.innerText = 'هیچ فایلی انتخاب نشده است';

                                // بازخوانی امن کامپوننت بدون ریلود کل صفحه
                                @this.dispatch('$refresh');
                            }, 1500);
                        }
                    }).catch(error => {
                        console.error('Livewire Error:', error);
                        progressBar.classList.add('bg-danger');
                        statusText.innerText = 'خطا در ارتباط با سرور یا انقضای نشست (Session). لطفاً صفحه را ریفرش کنید.';
                        startBtn.disabled = false;
                    });
                };

                fileReader.readAsArrayBuffer(blob);
            }
        });
    </script>
</div>
