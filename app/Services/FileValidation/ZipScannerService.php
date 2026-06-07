<?php

namespace App\Services\FileValidation;

use Exception;
use ZipArchive;

class ZipScannerService
{
    protected const MAX_FILES = 500;

    protected const MAX_UNCOMPRESSED_SIZE =
        1024 * 1024 * 1024; // 1GB

    protected const MAX_COMPRESSION_RATIO = 100;

    protected array $blockedExtensions = [

        'php',
        'phtml',
        'phar',

        'exe',
        'dll',
        'com',
        'bat',
        'cmd',

        'ps1',
        'sh',

        'js',
        'jar',

        'msi',
    ];

    public function scan(string $absolutePath): void
    {
        $zip = new ZipArchive();

        if (
            $zip->open($absolutePath)
            !== true
        ) {
            throw new Exception(
                'فایل ZIP معتبر نیست'
            );
        }

        $totalUncompressedSize = 0;

        if ($zip->numFiles > self::MAX_FILES) {

            $zip->close();

            throw new Exception(
                'تعداد فایل‌های ZIP بیش از حد مجاز است'
            );
        }

        for (
            $i = 0;
            $i < $zip->numFiles;
            $i++
        ) {

            $stat = $zip->statIndex($i);

            $fileName = $stat['name'];

            $extension = strtolower(
                pathinfo(
                    $fileName,
                    PATHINFO_EXTENSION
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Encrypted Zip
            |--------------------------------------------------------------------------
            */

            if (
                isset($stat['encryption_method'])
                &&
                $stat['encryption_method'] !==
                ZipArchive::EM_NONE
            ) {

                $zip->close();

                throw new Exception(
                    'ZIP رمزگذاری شده مجاز نیست'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Dangerous Extensions
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $extension,
                    $this->blockedExtensions
                )
            ) {

                $zip->close();

                throw new Exception(
                    "فایل غیرمجاز: {$fileName}"
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Nested Zip
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $extension,
                    ['zip','rar','7z']
                )
            ) {

                $zip->close();

                throw new Exception(
                    'ZIP تو در تو مجاز نیست'
                );
            }

            $size = $stat['size'] ?? 0;

            $compressed =
                $stat['comp_size'] ?? 1;

            $totalUncompressedSize += $size;

            /*
            |--------------------------------------------------------------------------
            | Compression Ratio
            |--------------------------------------------------------------------------
            */

            if ($compressed > 0) {

                $ratio =
                    $size / $compressed;

                if (
                    $ratio >
                    self::MAX_COMPRESSION_RATIO
                ) {

                    $zip->close();

                    throw new Exception(
                        'Zip Bomb شناسایی شد'
                    );
                }
            }
        }

        if (
            $totalUncompressedSize >
            self::MAX_UNCOMPRESSED_SIZE
        ) {

            $zip->close();

            throw new Exception(
                'حجم فایل‌های ZIP بیش از حد مجاز است'
            );
        }

        $zip->close();
    }
}
