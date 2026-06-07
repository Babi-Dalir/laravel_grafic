<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileManager;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FileValidation\ZipScannerService;
use Illuminate\Support\Facades\DB;

class ProductFileController extends Controller
{
    public function index(Product $product)
    {
        $files = $product
            ->files()
            ->latest()
            ->get();

        return view('admin.products.files.index',compact('product', 'files'));
    }

    public function store(Request $request, Product $product, ZipScannerService $scanner)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:512000',
            ],
            'title' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $tempName = null;

        DB::beginTransaction();

        try {
            $uploadedFile = $request->file('file');

            /*
            |--------------------------------------------------------------------------
            | Validate Extension (BEFORE anything)
            |--------------------------------------------------------------------------
            */
            $extension = strtolower($uploadedFile->getClientOriginalExtension());

            if (! in_array($extension, config('uploads.allowed_extensions'))) {
                throw new \Exception('فرمت فایل مجاز نیست');
            }

            /*
            |--------------------------------------------------------------------------
            | Store Temp File
            |--------------------------------------------------------------------------
            */
            $tempName = FileManager::storeTemp($uploadedFile);
            $tempPath = FileManager::tempPath($tempName);

            /*
            |--------------------------------------------------------------------------
            | Scan ZIP (if needed)
            |--------------------------------------------------------------------------
            */
            if ($extension === 'zip') {
                $scanner->scan($tempPath);
            }

            /*
            |--------------------------------------------------------------------------
            | Move to Final Storage
            |--------------------------------------------------------------------------
            */
            $storedName = FileManager::moveFromTemp(
                $tempName,
                $product->id
            );

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */
            $data = FileManager::metadata($uploadedFile);

            /*
            |--------------------------------------------------------------------------
            | Save DB
            |--------------------------------------------------------------------------
            */
            ProductFile::create([
                'product_id' => $product->id,
                'title' => $request->title,
                'stored_name' => $storedName,
                ...$data,
                'is_default' => ! $product->files()->exists(),
            ]);

            DB::commit();

            return back()->with('success', 'فایل با موفقیت آپلود شد');

        } catch (\Throwable $e) {

            DB::rollBack();

            /*
            |--------------------------------------------------------------------------
            | Cleanup temp file if exists
            |--------------------------------------------------------------------------
            */
            if ($tempName) {
                Storage::disk('digital_files')->delete(
                    "tmp/products/{$tempName}"
                );
            }

            return back()->withErrors([
                'file' => $e->getMessage(),
            ]);
        }
    }
    public function destroy(ProductFile $file)
    {
        Storage::disk('digital_files')
            ->delete(
                $file->path
            );

        $file->delete();

        return back()
            ->with(
                'success',
                'فایل حذف شد'
            );
    }
    public function setDefault(ProductFile $file)
    {
        ProductFile::query()
            ->where(
                'product_id',
                $file->product_id
            )
            ->update([
                'is_default' => false
            ]);

        $file->update([
            'is_default' => true
        ]);

        return back();
    }
}
