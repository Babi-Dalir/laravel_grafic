<?php

namespace App\Services\FileValidation;

use Exception;
use ZipArchive;
use Illuminate\Support\Facades\Log;

class ZipScannerService
{
    protected const MAX_FILES = 500;
    protected const MAX_UNCOMPRESSED_SIZE = 1073741824; // 1 GB
    protected const MAX_COMPRESSION_RATIO = 100;

    protected array $blockedExtensions = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'exe', 'dll', 'msi', 'bat', 'cmd', 'sh', 'js', 'jar', 'py', 'rb'
    ];

    public function scan(string $absolutePath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($absolutePath) !== true) {
            throw new Exception('فایل ZIP شما مخدوش یا رمزگذاری شده است و امکان باز کردن آن وجود ندارد.');
        }

        try {
            if ($zip->numFiles > self::MAX_FILES) {
                throw new Exception('تعداد فایل‌های داخل آرشیو ZIP بیش از حد مجاز (' . self::MAX_FILES . ' عدد) است. لطفاً فایل‌ها را بسته‌بندی‌تر کنید.');
            }

            $totalSize = 0;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (!$stat) continue;

                $name = str_replace('\\', '/', $stat['name']);
                $name = ltrim($name);

                if ($this->isTraversal($name)) {
                    throw new Exception('ساختار پوشه‌بندی داخل ZIP غیرمجاز و ناامن است (حملات مسیر).');
                }

                if (str_contains($name, '__MACOSX') || str_contains($name, '.DS_Store') || str_contains($name, 'Thumbs.db')) {
                    continue;
                }

                $lowercaseName = strtolower($name);
                foreach ($this->blockedExtensions as $blockedExt) {
                    if (str_contains($lowercaseName, '.' . $blockedExt)) {
                        throw new Exception("فایل ناامن با پسوند ممنوعه (.{$blockedExt}) در داخل زیپ کشف شد. آپلود متوقف شد.");
                    }
                }

                $size = (int) ($stat['size'] ?? 0);
                $comp = (int) ($stat['comp_size'] ?? 0);
                $totalSize += $size;

                if ($totalSize > self::MAX_UNCOMPRESSED_SIZE) {
                    throw new Exception('حجم فایل‌ها پس از خارج شدن از حالت فشرده، فراتر از حد مجاز سرور (۱ گیگابایت) خواهد رفت.');
                }

                if ($comp > 0) {
                    $ratio = $size / $comp;
                    if ($ratio > self::MAX_COMPRESSION_RATIO) {
                        Log::warning('Zip bomb detected', ['file' => $name, 'ratio' => $ratio]);
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
