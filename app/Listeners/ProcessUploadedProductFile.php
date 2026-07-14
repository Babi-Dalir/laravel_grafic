<?php

namespace App\Listeners;

use App\Enums\UploadFileStatus;
use App\Events\ProductFileUploaded;
use App\Models\Product;
use App\Models\ProductFile;
use App\Services\Upload\FileAssemblerService;
use App\Services\Upload\DigitalStorageService;
use App\Services\FileValidation\ZipScannerService;
use App\Services\ProductFileUploadService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessUploadedProductFile implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $backoff = 60;

    protected ZipScannerService $zipScanner;
    protected FileAssemblerService $assembler;
    protected DigitalStorageService $storageService;
    protected ProductFileUploadService $uploadValidation;

    public function __construct(
        ZipScannerService        $zipScanner,
        FileAssemblerService     $assembler,
        DigitalStorageService    $storageService,
        ProductFileUploadService $uploadValidation
    ) {
        $this->zipScanner = $zipScanner;
        $this->assembler = $assembler;
        $this->storageService = $storageService;
        $this->uploadValidation = $uploadValidation;
    }

    public function handle(ProductFileUploaded $event): void
    {
        $lockKey = "lock:process_file:{$event->fileUuid}";
        $lock = Cache::lock($lockKey, 600);

        if (!$lock->get()) {
            $this->release(10);
            return;
        }

        try {
            $product = Product::find($event->productId);
            if (!$product) {
                $this->closePipeline($event->tempName, $event->fileUuid);
                $lock->release();
                return;
            }

            $extension = strtolower(trim(pathinfo($event->originalName, PATHINFO_EXTENSION)));
            $localPath = $this->storageService->getLocalPath($event->tempName);

            if (!file_exists($localPath)) {
                throw new Exception("فایل نهایی موقت روی هارد سرور یافت نشد.");
            }

            $actualFileSize = filesize($localPath);

            if ($actualFileSize <= 0) {
                throw new Exception('فایل منتقل شده خالی و فاقد اطلاعات است.');
            }

            $maxLimit = 3 * 1024 * 1024 * 1024; // سقف مجاز ۳ گیگابایت
            if ($actualFileSize > $maxLimit) {
                throw new Exception('حجم فایل فرستاده شده فراتر از سقف مجاز سیستم پردازش است.');
            }

            // هش‌گیری سریع از روی دیسک لوکال
            $hash = hash_file('sha256', $localPath);

            // ۱. 🟢 انتقال ثبت و بروزرسانی دیتابیس به اولِ خط برای رفع دائم خطای Duplicate Entry
            $productFile = DB::transaction(function () use ($product, $event, $extension, $actualFileSize, $hash) {
                return ProductFile::updateOrCreate(
                    ['stored_name' => $event->tempName],
                    [
                        'product_id' => $product->id,
                        'title' => $event->title,
                        'original_name' => $event->originalName,
                        'extension' => $extension,
                        'sha256' => $hash, // فیلد هش بلافاصله مقدار واقعی و یکتا می‌گیرد
                        'size' => $actualFileSize,
                        'is_default' => !$product->files()->where('status', UploadFileStatus::Ready->value)->exists(),
                        'status' => UploadFileStatus::Processing->value,
                    ]
                );
            });

            // جلوگیری از ثبت مجدد فایل تکراری با هش یکسان (غیر از رکوردی که همین الان آپدیت کردیم)
            $duplicateExists = ProductFile::where('product_id', $product->id)
                ->where('sha256', $hash)
                ->where('id', '!=', $productFile->id)
                ->where('status', UploadFileStatus::Ready->value)
                ->exists();

            if ($duplicateExists) {
                throw new Exception('این فایل دقیقاً قبلاً آپلود شده و در مخزن موجود است.');
            }

            // تشخیص مایم‌تایپ
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if (!$finfo) {
                throw new Exception('امکان بارگذاری اکستنشن تشخیص مایم‌تایپ سرور (finfo) وجود ندارد.');
            }
            $realMime = finfo_file($finfo, $localPath);
            finfo_close($finfo);

            if (!$realMime) {
                throw new Exception('مایم‌تایپ باینری فایل قابل تشخیص نیست.');
            }

            Log::info('MIME CHECK', [
                'original'  => $event->originalName,
                'extension' => $extension,
                'mime'      => $realMime,
                'size'      => $actualFileSize,
            ]);

            // بروزرسانی نوع مایم در مدل دیتابیس
            // بروزرسانی نوع مایم در مدل دیتابیس
            $productFile->update(['mime_type' => $realMime]);

            // ۲. 🟢 اعتبارسنجی هوشمند مایم‌تایپ با نادیده گرفتن استثنایی فایل‌های مهندسی/طراحی (بدون چک کردن RAR)
            $bypassMimeCheck = in_array($extension, ['cdr', 'cdt', 'cmx', 'cpt', 'stl', 'obj', '3ds', 'stp', 'step', 'dxf']);

            if (!$bypassMimeCheck && !$this->uploadValidation->isValidMimeForExtension($extension, $realMime)) {
                throw new Exception('محتوای باینری فایل با پسوند آن همخوانی ندارد.');
            }

            // اسکن عمیق امضای باینری برای فایل‌های فشرده (ZIP / CDR)
            $this->zipScanner->scan($localPath, $extension);

            // انتقال استریم فایل به استوریج نهایی
            $this->storageService->streamToFinalStorage($event->tempName, $product->id);

            // به روز رسانی موفقیت‌آمیز وضعیت در دیتابیس
            $productFile->update(['status' => UploadFileStatus::Ready->value]);

            $this->storageService->deleteFromTemp($event->tempName);

        } catch (\Throwable $e) {
            ProductFile::where('stored_name', $event->tempName)->update([
                'status' => UploadFileStatus::Failed->value,
                'failure_reason' => mb_substr($e->getMessage(), 0, 500)
            ]);

            try {
                $this->storageService->deleteAbsoluteFile($event->productId, $event->tempName);
            } catch (\Throwable $subException) {}

            throw $e;
        } finally {
            $this->assembler->cleanChunks($event->fileUuid);
            $lock->release();
        }
    }

    public function failed(ProductFileUploaded $event, \Throwable $exception): void
    {
        Log::critical("شکست قطعی شلیک نهایی پایپ‌لاین آپلود فایل محصول شماره {$event->productId}: " . $exception->getMessage());

        ProductFile::where('stored_name', $event->tempName)->update([
            'status' => UploadFileStatus::Failed->value,
            'failure_reason' => 'سیستم پس از ۳ بار تلاش متوالی نتوانست فایل را پردازش کند.'
        ]);

        $this->storageService->deleteFromTemp($event->tempName);
        $this->assembler->cleanChunks($event->fileUuid);
    }

    private function closePipeline(string $tempName, string $uuid): void
    {
        $this->storageService->deleteFromTemp($tempName);
        $this->assembler->cleanChunks($uuid);
    }
}
