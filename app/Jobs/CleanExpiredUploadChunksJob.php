<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CleanExpiredUploadChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Job روی صف uploads اجرا می‌شود.
     */
    public function __construct()
    {
        $this->onQueue('uploads');
    }

    public function handle(): void
    {
        $disk = Storage::disk('digital_files');

        $targetFolder = 'tmp/products';

        /*
         * اگر فولدر اصلی وجود ندارد،
         * کاری برای انجام وجود ندارد.
         */
        if (! $disk->exists($targetFolder)) {
            return;
        }

        /*
         * دریافت فولدرهای موقت
         */
        $directories = $disk->directories($targetFolder);

        foreach ($directories as $directory) {

            try {

                /*
                 * فایل‌های داخل فولدر
                 */
                $files = $disk->files($directory);

                /*
                 * اگر فولدر خالی است،
                 * مستقیماً حذف شود.
                 */
                if (empty($files)) {
                    $disk->deleteDirectory($directory);

                    continue;
                }

                /*
                 * پیدا کردن آخرین زمان تغییر فایل‌ها
                 */
                $lastModifiedTime = 0;

                foreach ($files as $file) {

                    $modifiedTime = $disk->lastModified($file);

                    if ($modifiedTime > $lastModifiedTime) {
                        $lastModifiedTime = $modifiedTime;
                    }
                }

                /*
                 * اگر بیشتر از 24 ساعت از آخرین
                 * تغییر گذشته باشد، فولدر حذف می‌شود.
                 */
                $expirationTime = 86400;

                if (
                    time() - $lastModifiedTime
                    > $expirationTime
                ) {
                    $disk->deleteDirectory($directory);

                    Log::info(
                        "فولدر موقت آپلود حذف شد: {$directory}"
                    );
                }

            } catch (Throwable $exception) {

                /*
                 * خطای یک فولدر نباید باعث شود
                 * پاکسازی کل Job متوقف شود.
                 */
                Log::error(
                    "خطا در پاکسازی چانک موقت: {$directory}",
                    [
                        'directory' => $directory,
                        'error' => $exception->getMessage(),
                        'exception' => $exception::class,
                    ]
                );
            }
        }
    }
}
