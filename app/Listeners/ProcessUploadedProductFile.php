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
    )
    {
        $this->zipScanner = $zipScanner;
        $this->assembler = $assembler;
        $this->storageService = $storageService;
        $this->uploadValidation = $uploadValidation;
    }

    public function handle(ProductFileUploaded $event): void
    {
        // 🔒 ۱. جلوگیری از Race Condition با Atomic Lock
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

            $extension = strtolower(pathinfo($event->originalName, PATHINFO_EXTENSION));
            $localPath = $this->storageService->getLocalPath($event->tempName);

            if (!file_exists($localPath)) {
                throw new Exception("فایل نهایی موقت روی هارد سرور یافت نشد.");
            }

            // چک کردن آیدامپوتنسی
            $alreadyDone = ProductFile::where('stored_name', $event->tempName)->where('status', UploadFileStatus::Ready->value)->exists();
            if ($alreadyDone) {
                $this->assembler->cleanChunks($event->fileUuid);
                $lock->release();
                return;
            }

            $actualFileSize = filesize($localPath);

            // ۲. بررسی‌های ساختاری امنیتی
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $localPath);
            finfo_close($finfo);

            if (!$this->uploadValidation->isValidMimeForExtension($extension, $realMime)) {
                throw new Exception('محتوای باینری فایل با پسوند آن همخوانی ندارد.');
            }

            if ($extension === 'zip') {
                $this->zipScanner->scan($localPath);
            }

            $hash = hash_file('sha256', $localPath);
            if (ProductFile::where(['product_id' => $product->id, 'sha256' => $hash])->where('status', UploadFileStatus::Ready->value)->exists()) {
                throw new Exception('این فایل دقیقاً قبلاً آپلود شده و در مخرن موجود است.');
            }

            // ۳. ایجاد اولیه رکورد در دیتابیس
            $productFile = ProductFile::updateOrCreate(
                ['stored_name' => $event->tempName],
                [
                    'product_id' => $product->id,
                    'title' => $event->title,
                    'original_name' => $event->originalName,
                    'extension' => $extension,
                    'mime_type' => $realMime,
                    'size' => $actualFileSize,
                    'sha256' => $hash,
                    'is_default' => !$product->files()->where('status', UploadFileStatus::Ready->value)->exists(),
                    'status' => UploadFileStatus::Processing->value,
                ]
            );

            // ۴. انتقال استریم باینری خارج از تراکنش دیتابیس
            $this->storageService->streamToFinalStorage($event->tempName, $product->id);

            // ... کدهای قبلی مرحله ۳ و ۴ متد هندل شما

            // ۵. ارتقای نهایی وضعیت سند
            $productFile->update(['status' => UploadFileStatus::Ready->value]);

            // 🟢 حذف از تمپ فقط و فقط در صورت موفقیت کامل پایپ‌لاین
            $this->storageService->deleteFromTemp($event->tempName);

        } catch (\Throwable $e) {
            // ثبت وضعیت شکست برای این پارت در دیتابیس
            ProductFile::where('stored_name', $event->tempName)->update([
                'status' => UploadFileStatus::Failed->value,
                'failure_reason' => mb_substr($e->getMessage(), 0, 500)
            ]);

            // ❌ حذف متد deleteFromTemp از اینجا!
            // فایل باید روی هارد بماند تا اگر جاب دوباره تلاش کرد (Retry)، بتوند فایل را بخواند.

            $this->storageService->deleteAbsoluteFile($event->productId, $event->tempName);

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

        // 🔥 اصلاح فیکس متغیرها: فراخوانی از بدنه آبجکت $event
        $this->storageService->deleteFromTemp($event->tempName);
        $this->assembler->cleanChunks($event->fileUuid);
    }

    private function closePipeline(string $tempName, string $uuid): void
    {
        $this->storageService->deleteFromTemp($tempName);
        $this->assembler->cleanChunks($uuid);
    }
}
