<?php

namespace App\Services;

use App\Helpers\FileManager;
use App\Models\Product;
use App\Models\ProductFile;
use App\Services\FileValidation\ZipScannerService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProductFileUploadService
{
    public function upload(
        UploadedFile $uploadedFile,
        Product $product,
        ?string $title = null
    ): ProductFile {

        return DB::transaction(function () use (
            $uploadedFile,
            $product,
            $title
        ) {

            $extension = strtolower(
                $uploadedFile->getClientOriginalExtension()
            );

            if (
                ! in_array(
                    $extension,
                    config('uploads.allowed_extensions')
                )
            ) {
                throw new Exception(
                    'فرمت فایل مجاز نیست'
                );
            }

            $tempName = FileManager::storeTemp(
                $uploadedFile
            );

            try {

                $tempPath = FileManager::tempPath(
                    $tempName
                );

                if ($extension === 'zip') {

                    app(ZipScannerService::class)
                        ->scan($tempPath);
                }

                $hash = hash_file(
                    'sha256',
                    $tempPath
                );

                $exists = ProductFile::query()
                    ->whereHas('product', function ($q) {
                        $q->where(
                            'user_id',
                            auth()->id()
                        );
                    })
                    ->where('sha256', $hash)
                    ->exists();

                if ($exists) {

                    throw new Exception(
                        'این فایل قبلاً توسط شما آپلود شده است'
                    );
                }

                $storedName =
                    FileManager::moveFromTemp(
                        $tempName,
                        $product->id
                    );

                $data = FileManager::metadata(
                    $uploadedFile,
                    $hash
                );

                return ProductFile::create([

                    'product_id' => $product->id,

                    'title' => $title,

                    'stored_name' => $storedName,

                    ...$data,

                    'is_default' =>
                        ! $product
                            ->files()
                            ->exists(),
                ]);

            } catch (\Throwable $e) {

                \Storage::disk('digital_files')
                    ->delete(
                        "tmp/products/{$tempName}"
                    );

                throw $e;
            }
        });
    }
}
