<div>
    {{-- هدرها و آلرت‌های وضعیت پیام سیستم --}}
    @if (session()->has('message'))
        <div class="alert-modern alert-modern-success" role="alert">
            <i class="fa fa-check-circle alert-icon"></i>
            <span class="alert-text">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert-modern alert-modern-danger" role="alert">
            <i class="fa fa-exclamation-circle alert-icon"></i>
            <span class="alert-text">{{ session('error') }}</span>
        </div>
    @endif

    <div class="modern-wrapper">
        {{-- هدر --}}
        <div class="header-section">
            <h4 class="page-title">مدیریت فایل‌های محصول</h4>
            <p class="page-subtitle">محصول فعلی: <span class="product-name-highlight">{{ $product->name }}</span></p>
        </div>

        {{-- بخش باکس آپلود هوشمند --}}
        <div class="upload-box" wire:ignore>
            {{-- 🟢 هشدار ثابت: راهنمایی فایل‌های سنگین --}}
            <div class="alert-modern alert-modern-warning mb-3" role="alert"
                 style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 16px; padding: 15px; display: flex; align-items: center; gap: 12px;">
                <i class="fa fa-info-circle text-warning" style="font-size: 20px;"></i>
                <span class="alert-text" style="color: #92400e; font-size: 13.5px; font-weight: 600;">
                    توصیه مهم: برای پایداری بیشتر در فرآیند آپلود و حفظ ساختار لایه‌ها، پیشنهاد می‌شود فایل‌های سنگین و حجیم خود را حتماً به صورت فشرده (فرمت ZIP) آپلود نمایید.
                </span>
            </div>

            {{-- 🟢 هشدار داینامیک: عدم رفرش صفحه --}}
            <div id="refresh-danger-alert" class="alert-modern alert-modern-danger mb-4 d-none" role="alert"
                 style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 16px; padding: 15px; display: flex; align-items: center; gap: 12px; animation: pulse 2s infinite;">
                <i class="fa fa-exclamation-triangle text-danger" style="font-size: 20px;"></i>
                <span class="alert-text" style="color: #991b1b; font-size: 13.5px; font-weight: 700;">
                    هشدار حیاتی: فرآیند قطعه‌بندی باینری در حال انجام است؛ به هیچ عنوان صفحه را رفرش نکنید یا دکمه بازگشت را نزنید تا آپلود مخدوش نشود!
                </span>
            </div>
            <div class="row">
                <div class="col-12 mb-4 text-left">
                    <label class="input-label">عنوان نمایشی فایل <span class="optional-tag">(اختیاری)</span></label>
                    <input type="text" id="file-title" class="input-modern w-100"
                           placeholder="مثلاً: منبع لایه‌باز اصلی، نمونه سه‌بعدی خروجی...">
                </div>

                <div class="col-12">
                    <div id="upload-container" class="custom-dropzone">
                        <div class="upload-icon-wrapper">
                            <i class="fa fa-cloud-upload"></i>
                        </div>
                        <h5 class="dropzone-title" id="selected-file-info">فایل خود را به این‌جا بکشید یا کلیک کنید</h5>
                        <p class="dropzone-subtitle">حداکثر حجم پارت‌های ارسالی بهینه شده است</p>
                        <button type="button" class="d-none" id="browse-btn"></button>
                    </div>
                </div>
            </div>

            {{-- بخش فرمت‌ها و دکمه شروع آپلود در یک ردیف ریسپانسیو --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 style-gap">
                {{-- بخش فرمت‌ها در یک ردیف ریسپانسیو --}}
                <div class="text-left w-100 mb-3 mb-md-0">
                    <small class="formats-label">پسوندهای ساختاری مجاز سیستم:</small>
                    <div class="badge-container">
                        @foreach(config('uploads.allowed_extensions', ['dxf', 'png', 'jpg', 'jpeg', 'cdr', 'cdt', 'cmx', 'cpt', 'art', 'svg', 'webp', 'tiff', 'stl', 'obj', '3ds', 'stp', 'step', 'zip', 'psd', 'ai', 'eps', 'pdf', 'ttf', 'otf']) as $extension)
                            <span class="badge-format">{{ strtoupper($extension) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="action-btn-container">
                    <button type="button" id="start-upload-btn" class="btn-action btn-upload-start" disabled>
                        <i class="fa fa-rocket"></i> شروع آپلود فایل حجیم
                    </button>
                </div>
            </div>

            {{-- لایه پیشرفت نئونی جذاب --}}
            <div id="progress-wrapper" class="progress-section d-none">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="progress-badge" id="progress-percent">0%</span>
                    <small class="progress-status" id="upload-status-text">در حال تفکیک باینری پارت‌ها...</small>
                </div>
                <div class="neon-progress">
                    <div id="upload-progress-bar" class="neon-progress-bar"></div>
                </div>
            </div>
        </div>

        {{-- لیست فایل‌های آپلود شده --}}
        <div class="table-container" @if($isProcessing) wire:poll.3s="checkProcessingStatus" @endif>
            <div class="table-responsive">
                <table class="table table-modern mb-0 align-middle responsive-table">
                    <thead>
                    <tr>
                        <th style="width: 80px;">ردیف</th>
                        <th>مشخصات و عنوان فایل</th>
                        <th style="width: 120px;">پسوند</th>
                        <th style="width: 140px;">حجم دقیق</th>
                        <th style="width: 200px;" class="text-center">وضعیت سند</th>
                        <th style="width: 100px;" class="text-center">حذف</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($files as $index => $file)
                        <tr wire:key="file-row-{{ $file->id }}">
                            <td data-label="ردیف" class="row-number">{{ $files->firstItem() + $index }}</td>
                            <td data-label="مشخصات فایل">
                                <div
                                    class="file-details-wrapper d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon-box">
                                            <i class="fa fa-file-archive-o"></i>
                                        </div>
                                        <div class="file-meta mr-2">
                                            <span
                                                class="file-title-text">{{ $file->title ?? 'بدون عنوان تعریفی' }}</span>
                                            <small class="file-name-text"
                                                   title="{{ $file->original_name }}">{{ $file->original_name }}</small>
                                        </div>
                                    </div>

                                    {{-- 🟢 دکمه دانلود اختصاصی مدیریت: بر اساس متد کنترلر و روت اختصاصی سیستم شما بدون آسیب به کانت و لاگ فروشنده --}}
                                    @if(auth()->check() && auth()->user()->hasRole('مدیر'))
                                        <div class="download-action-box">
                                            <a href="{{ route('product.files.download', $file) }}"
                                               class="btn btn-sm text-white px-3"
                                               style="gap: 6px; font-size: 11px; font-weight: bold; background-color: #10b981; border: none; border-radius: 8px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);"
                                               title="دانلود مستقیم جهت بررسی کیفیت و محتوای فنی فایل">
                                                <i class="fa fa-download"></i>
                                                <span>دانلود بررسی مدیر</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td data-label="پسوند">
                                <span class="badge-extension">{{ $file->extension }}</span>
                            </td>
                            <td data-label="حجم دقیق"
                                class="file-size-text">{{ number_format($file->size / 1024 / 1024, 2) }} MB
                            </td>

                            {{-- ستون وضعیت سند متصل به State Machine دیتابیس --}}
                            <td data-label="وضعیت سند" class="text-center">
                                @if($file->status === 'ready')
                                    @if($file->is_default)
                                        <span class="badge-status-active">
                                            <i class="fa fa-star ml-1"></i> فایل اصلی محصول
                                        </span>
                                    @else
                                        <button wire:click="setDefault({{ $file->id }})" class="btn-set-default">
                                            انتخاب به عنوان اصلی
                                        </button>
                                    @endif
                                @elseif($file->status === 'failed')
                                    <span class="badge-status-failed text-danger" title="{{ $file->failure_reason }}"
                                          style="cursor: help;">
                                        <i class="fa fa-times-circle ml-1"></i> خطای پردازش امنیتی
                                    </span>
                                @else
                                    <span class="badge-status-processing text-warning">
                                        <i class="fa fa-spinner fa-spin ml-1"></i> در حال پردازش سرور...
                                    </span>
                                @endif
                            </td>

                            <td data-label="حذف" class="text-center">
                                <button type="button"
                                        class="btn-delete-file"
                                        title="حذف"
                                        @if(in_array($file->status, ['uploading', 'processing'])) disabled
                                        style="opacity: 0.5; cursor: not-allowed;" @endif
                                        onclick="openDeleteModal({{ $file->id }}, '{{ $file->title ?? $file->original_name }}')">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state-row">
                                <i class="fa fa-folder-open-o empty-icon"></i>
                                <p class="empty-text">هنوز هیچ فایلی برای این محصول آپلود نشده یا فایل‌های ارسالی در صف
                                    پردازش هستند...</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($files->hasPages())
                <div class="pagination-wrapper">
                    {{ $files->appends(Request::except('page'))->links() }}
                </div>
            @endif
        </div>

        {{-- دکمه ثبت نهایی معلق شیک --}}
        @if($files->count())
            <div class="mt-4 text-left">
                <button wire:click="submitForReview" class="btn-submit-final">
                    ثبت نهایی محصول و ارسال به بررسی <i class="fa fa-chevron-left mr-2"></i>
                </button>
            </div>
        @endif
    </div>

    {{-- ساختار مودال حذف مدرن اختصاصی --}}
    <div id="delete-confirmation-modal" class="custom-modal-backdrop d-none" onclick="closeDeleteModal(event)">
        <div class="custom-modal-content" onclick="event.stopPropagation()">
            <div class="modal-graphic-icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <h3 class="modal-confirm-title">حذف قطعی سند</h3>
            <p class="modal-confirm-text">
                آیا از حذف فایل <span id="modal-file-name-placeholder" class="text-danger font-weight-bold"></span>
                مطمئن هستید؟ این عملیات غیرقابل بازگشت است.
            </p>
            <div class="modal-actions-wrapper">
                <button type="button" class="btn-modal-action btn-modal-cancel" onclick="closeDeleteModal(null)">
                    انصراف و بازگشت
                </button>
                <button type="button" id="modal-confirm-delete-btn" class="btn-modal-action btn-modal-confirm">
                    بله، حذف شود
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    const browseBtn = document.getElementById('browse-btn');
    const uploadContainer = document.getElementById('upload-container');
    const startBtn = document.getElementById('start-upload-btn');
    const fileInfo = document.getElementById('selected-file-info');
    const progressWrapper = document.getElementById('progress-wrapper');
    const progressBar = document.getElementById('upload-progress-bar');
    const progressPercent = document.getElementById('progress-percent');
    const statusText = document.getElementById('upload-status-text');
    const titleInput = document.getElementById('file-title');
    const refreshDangerAlert = document.getElementById('refresh-danger-alert');
    let completed = false;
    let isUploadingCurrently = false;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // 🟢 کدهای جدید را دقیقاً اینجا بگذارید:
    const allowedExts = {!! json_encode(config('uploads.allowed_extensions', ['dxf', 'png', 'jpg', 'jpeg', 'cdr', 'cdt', 'cmx', 'cpt', 'art', 'svg', 'webp', 'tiff', 'stl', 'obj', '3ds', 'stp', 'step', 'zip', 'psd', 'ai', 'eps', 'pdf', 'ttf', 'otf'])) !!};

    const r = new Resumable({
        target: '{{ route("product.upload-chunk", $product->id) }}',
        chunkSize: 2 * 1024 * 1024,
        forceChunkSize: true,
        simultaneousUploads: 1,
        testChunks: false,
        fileType: allowedExts, // 👈 اعمال محدودیت انتخاب فایل در ویندوز/مک/اندروید
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    r.assignBrowse(uploadContainer);

    window.addEventListener('beforeunload', function (e) {
        if (isUploadingCurrently) {
            e.preventDefault();
            e.returnValue = 'فرآیند آپلود فایل در حال انجام است. در صورت خروج، آپلود ناقص می‌ماند. آیا مطمئن هستید؟';
            return e.returnValue;
        }
    });

    r.on('fileAdded', function (file) {
        completed = false;
        const maxFourGigabytes = 4294967296;

        if (file.size > maxFourGigabytes) {
            startBtn.setAttribute('disabled', 'true');
            progressBar.style.width = '100%';
            progressBar.style.background = '#ef4444';
            progressPercent.innerText = 'خطا';
            progressWrapper.classList.remove('d-none');

            fileInfo.innerHTML = '<span class="text-danger font-weight-bold">خطای حجم غیرمجاز!</span>';
            statusText.style.color = '#b91c1c';
            statusText.innerHTML = `<i class="fa fa-exclamation-triangle ml-1"></i> حجم فایل انتخاب شده (${(file.size / 1024 / 1024 / 1024).toFixed(2)} GB) بیشتر از حد مجاز (۴ گیگابایت) است. لطفاً حجم فایل را کاهش داده و دوباره تلاش کنید.`;

            r.removeFile(file);
            return false;
        }

        fileInfo.innerHTML = '<i class="fa fa-file-text text-primary ml-2"></i> <span class="text-primary font-weight-bold">' + file.fileName + '</span> (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
        startBtn.removeAttribute('disabled');

        progressBar.style.width = '0%';
        progressBar.style.background = '';
        progressPercent.innerText = '0%';
        progressPercent.style.background = '';
        progressPercent.style.color = '';
        statusText.style.color = '';
        statusText.innerText = '';
        progressWrapper.classList.add('d-none');
    });

    startBtn.addEventListener('click', function () {
        if (r.files.length === 0) return;

        r.opts.query = {
            title: titleInput.value,
            resumableTotalSize: r.files[0].size
        };

        isUploadingCurrently = true;
        refreshDangerAlert.classList.remove('d-none');

        r.upload();
        startBtn.setAttribute('disabled', 'true');
        progressWrapper.classList.remove('d-none');
        statusText.innerText = 'در حال آماده‌سازی و تفکیک باینری پارت‌ها...';
    });

    r.on('fileProgress', function (file) {
        const progress = Math.floor(file.progress() * 100);
        progressBar.style.width = progress + '%';
        progressPercent.innerText = progress + '%';

        const totalChunks = file.chunks.length;
        let currentChunkIndex = Math.ceil(file.progress() * totalChunks);

        if (currentChunkIndex === 0 && totalChunks > 0) {
            currentChunkIndex = 1;
        }

        statusText.innerText = 'در حال ارسال قطعه پارت (' + currentChunkIndex + ' از ' + totalChunks + ')...';
    });

    r.on('fileSuccess', function (file, message) {
        try {
            const data = JSON.parse(message);
            if (data.status === 'success' && data.completed && !completed) {
                completed = true;

                isUploadingCurrently = false;
                refreshDangerAlert.classList.add('d-none');

                statusText.innerText = 'فایل پارت آخر با موفقیت منتقل و به صف پردازش نهایی ارسال شد.';
                r.removeFile(file);
                fileInfo.innerText = 'فایل خود را به این‌جا بکشید یا کلیک کنید';
                titleInput.value = '';
                $wire.dispatch('refresh-file-list');
            }
        } catch (e) {
            console.error('Error parsing response', e);
        }
    });

    r.on('fileError', function (file, message) {
        isUploadingCurrently = false;
        refreshDangerAlert.classList.add('d-none');

        progressBar.style.width = '100%';
        progressBar.style.background = '#ef4444';
        progressPercent.style.background = '#fef2f2';
        progressPercent.style.color = '#ef4444';
        progressPercent.innerText = 'خطا';

        let errorMessage = 'خطایی در ارتباط با سرور یا تایید پارت‌ها رخ داد. لطفا اتصال اینترنت خود را چک کنید.';
        if (message) {
            try {
                const response = JSON.parse(message);
                errorMessage = response.message || errorMessage;
            } catch (e) {
                if (typeof message === 'string' && message.length < 150) {
                    errorMessage = message;
                }
            }
        }

        statusText.style.color = '#b91c1c';
        statusText.innerHTML = '<i class="fa fa-exclamation-triangle ml-1"></i> ' + errorMessage;
        startBtn.removeAttribute('disabled');
    });

    let targetFileId = null;
    const deleteModal = document.getElementById('delete-confirmation-modal');
    const modalFileNamePlaceholder = document.getElementById('modal-file-name-placeholder');
    const modalConfirmDeleteBtn = document.getElementById('modal-confirm-delete-btn');

    window.openDeleteModal = function (id, name) {
        targetFileId = id;
        modalFileNamePlaceholder.innerText = `«${name}»`;
        deleteModal.classList.remove('d-none');
        document.body.style.overflow = 'hidden';
    }

    window.closeDeleteModal = function (event) {
        if (event === null || event.target === deleteModal) {
            deleteModal.classList.add('d-none');
            document.body.style.overflow = '';
            targetFileId = null;
        }
    }

    modalConfirmDeleteBtn.addEventListener('click', function () {
        if (targetFileId) {
            $wire.dispatch('destroy_product_file', [targetFileId]);
            closeDeleteModal(null);
        }
    });
</script>
@endscript
