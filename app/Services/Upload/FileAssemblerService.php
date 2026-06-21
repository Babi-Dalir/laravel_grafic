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

    public function storeChunk(UploadedFile $file, string $fileUuid, int $chunkIndex): string
    {
        $name = "chunk_{$chunkIndex}.part";
        $this->tmpDisk->putFileAs("chunks/{$fileUuid}", $file, $name);
        return $name;
    }

    public function combine(string $fileUuid, int $totalChunks, string $extension): string
    {
        $finalTempName = (string) Str::uuid() . '.' . $extension;

        // ایجاد فایل به صورت امن روی دیسک موقت محلی لاراول
        $localPath = $this->tmpDisk->path($finalTempName);
        $targetStream = fopen($localPath, 'wb');

        if (!$targetStream) {
            throw new Exception("امکان ایجاد استریم مقصد روی سرور وجود ندارد.");
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkRelativePath = "chunks/{$fileUuid}/chunk_{$i}.part";

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
        $folder = "chunks/{$fileUuid}";
        if ($this->tmpDisk->exists($folder)) {
            $this->tmpDisk->deleteDirectory($folder);
        }
    }
}
