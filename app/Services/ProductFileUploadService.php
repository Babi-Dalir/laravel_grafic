<?php
namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileManager;
use App\Services\FileValidation\ZipScannerService;

class ProductFileUploadService
{
    protected $zipScanner;

    public function __construct(ZipScannerService $zipScanner)
    {
        $this->zipScanner = $zipScanner;
    }

    public function uploadChunk(
        string $base64Data,
        string $fileUuid,
        int $chunkIndex,
        int $totalChunks,
        string $originalName,
        Product $product,
        ?string $title = null
    ): ?ProductFile {

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, config('uploads.allowed_extensions', ['zip', 'psd', 'rar']), true)) {
            throw new Exception('فرمت فایل انتخاب شده مجاز نیست.');
        }

        if (strlen($base64Data) > 8 * 1024 * 1024) {
            throw new Exception('حجم تکه ارسالی فراتر از ساختار مجاز است. لطفاً افزونه‌های تغییر دهنده سرعت مرورگر خود را خاموش کنید.');
        }

        // 🌟 حذف آرگومان دوم برای جلوگیری از خطای سایلنت در چانک‌های باینری طولانی ویندوز
        $binaryData = base64_decode($base64Data);

        if ($binaryData === false) {
            throw new Exception('داده ارسالی چانک معتبر نیست یا در طول مسیر ارسال مخدوش شده است.');
        }

        FileManager::storeChunkTempFromBinary($binaryData, $fileUuid, $chunkIndex);

        if ($chunkIndex + 1 < $totalChunks) {
            return null;
        }

        return DB::transaction(function () use ($fileUuid, $totalChunks, $originalName, $extension, $product, $title) {

            $tempName = FileManager::combineChunks($fileUuid, $totalChunks, $extension);
            $tempPath = FileManager::tempPath($tempName);

            try {
                // ۱. بررسی هویت واقعی فایل
                $realMime = FileManager::realMime($tempPath);
                if (!$this->isValidMimeForExtension($extension, $realMime)) {
                    throw new Exception('محتوای داخلی فایل با پسوند ظاهری آن مطابقت ندارد! فرستادن فایل‌های مخرب با پسوند جعلی ممنوع است.');
                }

                // ۲. اسکن فایل زیپ
                if ($extension === 'zip') {
                    $this->zipScanner->scan($tempPath);
                }

                // ۳. هش فایل نهایی
                $hash = hash_file('sha256', $tempPath);

                // ۴. بررسی تکراری نبودن
                $exists = ProductFile::where([
                    'product_id' => $product->id,
                    'sha256' => $hash,
                ])->exists();

                if ($exists) {
                    throw new Exception('این فایل دقیقاً با همین محتویات قبلاً برای این محصول آپلود شده است (فایل تکراری).');
                }

                // ۵. استخراج متاداتا و حجم فایل قبل از انتقال
                $metadata = FileManager::metadata($tempPath, $originalName, $extension, $hash);

                // ۶. انتقال به دایرکتوری دائمی
                $storedName = FileManager::moveFromTemp($tempName, $product->id);

                // ۷. ذخیره در دیتابیس
                return ProductFile::create([
                    'product_id' => $product->id,
                    'title' => $title,
                    'stored_name' => $storedName,
                    'is_default' => !$product->files()->exists(),
                    ...$metadata,
                ]);

            } catch (\Throwable $e) {
                Storage::disk('digital_files')->delete("tmp/products/{$tempName}");
                throw $e;
            } finally {
                FileManager::cleanTrackedChunks($fileUuid);
            }
        });
    }

    protected function isValidMimeForExtension(string $extension, string $mime): bool
    {
        $validMimes = [
            'zip' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
            'rar' => ['application/x-rar-compressed', 'application/x-rar', 'application/octet-stream'],
            'psd' => ['image/vnd.adobe.photoshop', 'application/x-photoshop', 'image/psd']
        ];

        if (!array_key_exists($extension, $validMimes)) {
            return false;
        }

        return in_array($mime, $validMimes[$extension], true);
    }

    public function delete(ProductFile $file)
    {
        FileManager::deleteDigitalFile($file->product_id, $file->stored_name);
        $file->delete();
    }
}
