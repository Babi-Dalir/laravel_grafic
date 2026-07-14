<?php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class FileAssemblerService
{
    protected $tmpDisk;

    public function __construct()
    {
        $this->tmpDisk = Storage::disk('digital_files_tmp');
    }

    /**
     * استخراج عدد واقعی از مقدار چانک ایندکس (حل مشکل رشته‌های غیرعددی مثل 512560-1dxf)
     */
    private function parseIndex($chunkIndex): int
    {
        // در صورتی که ساختاری مثل "512560-1dxf" ارسال شده باشد، عدد چانک واقعی را جدا می‌کند
        if (is_string($chunkIndex) && str_contains($chunkIndex, '-')) {
            $parts = explode('-', $chunkIndex);
            // فرض بر این است که بخش دوم ایندکس ترتیب چانک است. در غیر این صورت از مقدار اولیه عددی رشته استفاده می‌کند
            $possibleIndex = end($parts);
            return is_numeric($possibleIndex) ? (int)$possibleIndex : (int)$chunkIndex;
        }
        return (int) $chunkIndex;
    }

    public function storeChunk(UploadedFile $file, string $fileUuid, $chunkIndex): string
    {
        $index = $this->parseIndex($chunkIndex);
        $name = "chunk_{$index}.part";

        // پاک‌سازی UUID برای مسائل امنیتی دایرکتوری‌ها
        $safeUuid = preg_replace('/[^a-zA-Z0-9\-]/', '', $fileUuid);

        $this->tmpDisk->putFileAs("chunks/{$safeUuid}", $file, $name);
        return $name;
    }

    public function combine(string $fileUuid, int $totalChunks, string $extension): string
    {
        $finalTempName = (string) Str::uuid() . '.' . $extension;
        $localPath = $this->tmpDisk->path($finalTempName);
        $targetStream = fopen($localPath, 'wb');

        if (!$targetStream) {
            throw new Exception("امکان ایجاد استریم مقصد روی سرور وجود ندارد.");
        }

        $safeUuid = preg_replace('/[^a-zA-Z0-9\-]/', '', $fileUuid);

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkRelativePath = "chunks/{$safeUuid}/chunk_{$i}.part";

            if (!$this->tmpDisk->exists($chunkRelativePath)) {
                fclose($targetStream);
                @unlink($localPath);
                throw new Exception("تکه باینری شماره {$i} مفقود شده است.");
            }

            $chunkPath = $this->tmpDisk->path($chunkRelativePath);
            $chunkStream = fopen($chunkPath, 'rb');
            stream_copy_to_stream($chunkStream, $targetStream);

            fclose($chunkStream);
        }

        fclose($targetStream);
        return $finalTempName;
    }

    public function cleanChunks(string $fileUuid): void
    {
        $safeUuid = preg_replace('/[^a-zA-Z0-9\-]/', '', $fileUuid);
        $folder = "chunks/{$safeUuid}";
        if ($this->tmpDisk->exists($folder)) {
            $this->tmpDisk->deleteDirectory($folder);
        }
    }
}
