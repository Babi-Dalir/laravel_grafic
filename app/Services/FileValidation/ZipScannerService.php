<?php

namespace App\Services\FileValidation;

use Exception;
use ZipArchive;
use Illuminate\Support\Facades\Log;

class ZipScannerService
{
    protected const MAX_FILES = 2000;
    protected const MAX_UNCOMPRESSED_SIZE = 4294967296; // 4 GB
    protected const MAX_COMPRESSION_RATIO = 100;

    protected array $blockedExtensions = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'exe', 'dll', 'msi', 'bat', 'cmd', 'sh', 'js', 'jar', 'py', 'rb',
        'html', 'htm', 'xml'
    ];

    public function scan(string $absolutePath, ?string $extension = null): void
    {
        $extension = $extension
            ? strtolower(trim($extension))
            : strtolower(trim(pathinfo($absolutePath, PATHINFO_EXTENSION)));

        $corelExtensions = ['cdr', 'cdt', 'cmx', 'cpt'];

        if (in_array($extension, $corelExtensions, true)) {
            // امضای باینری سخت‌گیرانه فقط روی پسوند اصلی .cdr اعمال خواهد شد
            if ($extension === 'cdr') {
                $this->validateCorelStructure($absolutePath, $extension);
            }
            return;
        }

        if ($extension === 'zip') {
            $this->processZipVerification($absolutePath, 'zip');
        }
    }

    /**
     * 🔒 تایید اصالت امضا و فونداسیون باینری فایل‌های کورل اصلی
     */
    private function validateCorelStructure(string $absolutePath, string $extension): void
    {
        if (!file_exists($absolutePath) || filesize($absolutePath) < 4) {
            throw new Exception('فایل ارسالی مخدوش یا خالی است.');
        }

        // باز کردن امن هندل فایل با هندل‌کردن خطاهای سطح سیستم‌عامل
        $handle = @fopen($absolutePath, 'rb');
        if (!$handle) {
            throw new Exception('امکان خواندن ساختار فایل وجود ندارد.');
        }

        $bytes = fread($handle, 4);
        fclose($handle);

        $hex = bin2hex($bytes);

        // نرم‌تر کردن خطا برای سازگاری حداکثری با اکسپورت‌های کورل ضمن حفظ گارد امنیتی
        if ($hex !== '52494646' && $hex !== '504b0304') {
            if ($extension === 'cdr') {
                throw new Exception("ساختار باینری این فایل با استانداردهای نرم‌افزار CorelDRAW همخوانی ندارد.");
            }
        }

        // اگر فایل کورل جدید بر پایه ZIP فشرده شده بود، امنیت آرشیو آن اسکن می‌شود
        if ($hex === '504b0304') {
            $this->processZipVerification($absolutePath, 'cdr');
        }
    }

    /**
     * بررسی امنیت آرشیوهای زیپ و کورل‌های مدرن
     */
    private function processZipVerification(string $absolutePath, string $parentExtension): void
    {
        $zip = new ZipArchive();

        if ($zip->open($absolutePath) !== true) {
            throw new Exception('فایل فشرده مخدوش یا رمزگذاری شده است و امکان باز کردن آن وجود ندارد.');
        }

        // 🟢 تعیین لیست پسوندهای ممنوعه بر اساس نوع فایل اصلی برای جلوگیری از تداخل فایلهای کورل
        $currentBlockedExtensions = $this->blockedExtensions;
        if ($parentExtension === 'cdr') {
            $currentBlockedExtensions = array_diff($currentBlockedExtensions, ['xml', 'html', 'htm']);
        }

        try {
            if ($zip->numFiles > self::MAX_FILES) {
                throw new Exception('تعداد فایل‌های داخل آرشیو بیش از حد مجاز (' . self::MAX_FILES . ' عدد) است.');
            }

            $totalSize = 0;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (!$stat) continue;

                $name = str_replace('\\', '/', $stat['name']);
                $name = ltrim($name);

                if ($this->isTraversal($name)) {
                    throw new Exception('ساختار پوشه‌بندی داخل فایل ناامن است (حملات مسیر).');
                }

                if (str_contains($name, '__MACOSX') || str_contains($name, '.DS_Store') || str_contains($name, 'Thumbs.db')) {
                    continue;
                }

                $lowercaseName = strtolower($name);

                // ۱. تشخیص پسوند نهایی فایل داخل آرشیو با pathinfo برای صحت صددرصدی
                $finalExt = pathinfo($lowercaseName, PATHINFO_EXTENSION);
                if (in_array($finalExt, $currentBlockedExtensions, true)) {
                    throw new Exception("فایل ناامن با پسوند ممنوعه (.{$finalExt}) در داخل آرشیو کشف شد.");
                }

                // ۲. مکانیزم دفاعی در برابر ترفند پسوند دوگانه (Double Extension Bypass)
                $fileNameParts = explode('.', $lowercaseName);
                if (count($fileNameParts) > 2) {
                    array_pop($fileNameParts); // حذف پسوند نهایی از حلقه بررسی میانی
                    foreach ($fileNameParts as $part) {
                        if (in_array(trim($part), $currentBlockedExtensions, true)) {
                            throw new Exception("تلاش برای دور زدن سیستم با پسوند دوگانه مخرب (.{$part}) شناسایی شد.");
                        }
                    }
                }

                $size = (int) ($stat['size'] ?? 0);
                $comp = (int) ($stat['comp_size'] ?? 0);
                $totalSize += $size;

                if ($totalSize > self::MAX_UNCOMPRESSED_SIZE) {
                    throw new Exception('حجم فایل‌ها پس از خارج شدن از حالت فشرده، فراتر از حد مجاز سرور است.');
                }

                if ($comp > 0) {
                    $ratio = $size / $comp;
                    if ($ratio > self::MAX_COMPRESSION_RATIO) {
                        Log::warning('Zip bomb detected within package', ['file' => $name, 'ratio' => $ratio]);
                        throw new Exception('ساختار فایل فشرده مشکوک و خطرناک ارزیابی شد (آسیب‌پذیری Zip Bomb).');
                    }
                }
            }
        } finally {
            $zip->close();
        }
    }

    private function isTraversal(string $path): bool
    {
        return str_contains($path, '../')
            || str_contains($path, '..\\')
            || str_starts_with($path, '/')
            || preg_match('/^[A-Za-z]:\//', $path);
    }
}
