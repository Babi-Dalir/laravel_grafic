<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as IM;

class FileManager
{
    public static function saveResume($file,$user_id)
    {
        if (!$file) {
            return null;
        }

        return $file->store(
            "resume/{$user_id}",
            'files'
        );
    }
    //ذخیره موقت فایل
    public static function storeTemp(UploadedFile $file): string {

        $name = $file->hashName();

        Storage::disk('digital_files')
            ->putFileAs(
                'tmp/products',
                $file,
                $name
            );

        return $name;
    }
    //انتقال از Temp
    public static function moveFromTemp(string $tempName, int $productId): string {

        Storage::disk('digital_files')
            ->move(
                "tmp/products/{$tempName}",
                "products/{$productId}/{$tempName}"
            );

        return $tempName;
    }
    //مسیر فایل موقت
    public static function tempPath(string $tempName): string {

        return storage_path(
            "app/private/tmp/products/{$tempName}"
        );
    }
    public static function metadata(UploadedFile $file): array {

        return [

            'original_name' =>
                $file->getClientOriginalName(),

            'extension' =>
                strtolower(
                    $file->getClientOriginalExtension()
                ),

            'mime_type' =>
                $file->getMimeType(),

            'size' =>
                $file->getSize(),

            'sha256' =>
                hash_file(
                    'sha256',
                    $file->getRealPath()
                ),
        ];
    }

    public static function store(UploadedFile $file, int $productId): array {

        $storedName = $file->hashName();

        $path = "products/{$productId}";

        Storage::disk('digital_files')
            ->putFileAs(
                $path,
                $file,
                $storedName
            );

        return [
            'stored_name' => $storedName,
            'original_name' => $file->getClientOriginalName(),
            'extension' => strtolower(
                $file->getClientOriginalExtension()
            ),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'sha256' => hash_file(
                'sha256',
                $file->getRealPath()
            ),
        ];
    }

    public static function deleteDigitalFile($product_id, $fileName)
    {
        if ($fileName) {
            $path = 'products/' . $product_id . '/' . $fileName;

            if (Storage::disk('digital_files')->exists($path)) {
                Storage::disk('digital_files')->delete($path);
            }
        }
    }
}
