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

    // 🟢 اصلاح امضا بر اساس ساختار پیشنهادی شما
    public function scan(string $absolutePath, ?string $extension = null): void
    {
        $extension = $extension ?: strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        // گارد اختصاصی برای فایل‌های کورل دراو (CDR)
        if ($extension === 'cdr') {
            $this->validateCorelDrawStructure($absolutePath);
            return;
        }

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

    /**
     * 🔒 تایید اصالت امضا و ساختار داخلی فایل‌های کورل‌دراو بدون ریسک فیل شدن جاب
     */
    private function validateCorelDrawStructure(string $absolutePath): void
    {
        if (!file_exists($absolutePath) || filesize($absolutePath) < 4) {
            throw new Exception('فایل کورل دراو ارسالی مخدوش یا خالی است.');
        }

        // خواندن ۴ بایت اول فایل (Magic Number) جهت احراز هویت واقعی باینری
        $handle = fopen($absolutePath, 'rb');
        $bytes = fread($handle, 4);
        fclose($handle);

        $hex = bin2hex($bytes);

        // ۱. نسخه‌های قدیمی کورل با امضای باینری RIFF شروع می‌شوند (52494646)
        // ۲. نسخه‌های جدید کورل با امضای زیپ استاندارد PK شروع می‌شوند (504b0304)
        if ($hex !== '52494646' && $hex !== '504b0304') {
            throw new Exception('جعل پسوند مخدوش شناسایی شد! محتوای این فایل با پسوند .cdr همخوانی ندارد.');
        }

        // اگر کورل جدید بر پایه ZIP بود، مطمئن می‌شویم که پکیج مخدوش یا حاوی کدهای مخرب تزریقی نباشد
        if ($hex === '504b0304') {
            $zip = new ZipArchive();
            if ($zip->open($absolutePath) === true) {
                // ساختار داخلی فایل‌های کورل جدید حتماً باید شامل پوشه یا فایلی به نام content یا metadata یا root باشد
                $hasCorelSign = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (str_contains($name, 'content/') || str_contains($name, 'metadata/') || str_contains($name, 'content.xml')) {
                        $hasCorelSign = true;
                        break;
                    }
                }
                $zip->close();

                if (!$hasCorelSign) {
                    throw new Exception('امنیت فایل رد شد. این فایل یک زیپ عادی تغییر نام یافته به CDR است و فایل کورل دراو واقعی نیست.');
                }
            } else {
                throw new Exception('فایل کورل دراو ساختار فشرده مخدوشی دارد و باز نمی‌شود.');
            }
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
