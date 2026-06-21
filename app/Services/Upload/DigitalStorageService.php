<?php

namespace App\Services\Upload;

use Illuminate\Support\Facades\Storage;
use Exception;

class DigitalStorageService
{
    protected $tmpDisk;
    protected $finalDisk;

    public function __construct()
    {
        $this->tmpDisk = Storage::disk('digital_files_tmp');
        $this->finalDisk = Storage::disk('digital_files');
    }

    public function getLocalPath(string $tempName): string
    {
        // استفاده از متد تعبیه‌شده برای بازگرداندن مسیر فیزیکی لوکال
        return $this->tmpDisk->path($tempName);
    }

    public function streamToFinalStorage(string $tempName, int $productId): string
    {
        $targetPath = "products/{$productId}/{$tempName}";

        // 🚀 اصلاح مهم: خواندن استریم بدون وابستگی به متد path فیزیکی برای جلوگیری از کرش کلاود
        $localStream = $this->tmpDisk->readStream($tempName);

        if (!$localStream) {
            throw new Exception("امکان باز کردن استریم منبع فایل وجود ندارد.");
        }

        $uploaded = $this->finalDisk->writeStream($targetPath, $localStream);

        if (is_resource($localStream)) {
            fclose($localStream);
        }

        if (!$uploaded) {
            throw new Exception("خطا در کپی استریم به دیسک ابری نهایی.");
        }

        return $tempName;
    }

    public function deleteFromTemp(string $tempName): void
    {
        if ($this->tmpDisk->exists($tempName)) {
            $this->tmpDisk->delete($tempName);
        }
    }

    public function deleteAbsoluteFile(int $productId, string $fileName): void
    {
        $this->finalDisk->delete("products/{$productId}/{$fileName}");
    }
}
