<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanExpiredUploadChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'uploads';

    public function handle(): void
    {
        $disk = Storage::disk('digital_files');
        $targetFolder = 'tmp/products';

        if (! $disk->exists($targetFolder)) {
            return;
        }

        $directories = $disk->directories($targetFolder);

        foreach ($directories as $dir) {
            try {
                $files = $disk->files($dir);

                if (empty($files)) {
                    $disk->deleteDirectory($dir);
                    continue;
                }

                $lastModifiedTime = 0;
                foreach ($files as $file) {
                    $time = $disk->lastModified($file);
                    if ($time > $lastModifiedTime) {
                        $lastModifiedTime = $time;
                    }
                }

                // ۲۴ ساعت از آخرین چانک گذشته باشد
                if (time() - $lastModifiedTime > 86400) {
                    $disk->deleteDirectory($dir);
                }
            } catch (\Throwable $e) {
                Log::error("خطا در پاکسازی چانک موقت در مسیر: {$dir}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
