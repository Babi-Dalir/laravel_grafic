<?php

namespace App\Livewire\Admin\Products;

use App\Enums\ProductStatus;
use App\Helpers\FileManager;
use App\Models\Product;
use App\Models\ProductFile;
use App\Services\FileValidation\ZipScannerService;
use App\Services\ProductFileUploadService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ProductFileList extends Component
{
    use WithFileUploads;

    public Product $product;

    public $file;
    public $title;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function uploadFile(ProductFileUploadService $service)
    {
        $this->validate([
            'file' => 'required|file|max:512000',
            'title' => 'nullable|max:255',
        ]);

        try {

            $service->upload(
                $this->file,
                $this->product,
                $this->title
            );

            $this->reset([
                'file',
                'title'
            ]);

            session()->flash(
                'message',
                'فایل با موفقیت آپلود شد'
            );

        } catch (\Throwable $e) {

            $this->addError(
                'file',
                $e->getMessage()
            );
        }
    }

    public function deleteFile($id)
    {
        $file = ProductFile::findOrFail($id);

        Storage::disk('digital_files')
            ->delete($file->path);

        $wasDefault = $file->is_default;

        $productId = $file->product_id;

        $file->delete();

        if ($wasDefault) {

            ProductFile::query()
                ->where('product_id', $productId)
                ->first()
                ?->update([
                    'is_default' => true
                ]);
        }

        $productFolder = "products/{$file->product_id}";
        if (count(Storage::disk('digital_files')->files($productFolder)) === 0) {
            Storage::disk('digital_files')->deleteDirectory($productFolder);
        }
    }

    #[On('destroy_product_file')]
    public function destroyProductFile($fileId)
    {
        $this->deleteFile($fileId);
    }

    public function setDefault($id)
    {
        $file = ProductFile::findOrFail($id);

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
    }
    public function submitForReview()
    {
        $product = $this->product;

        if (!$product->galleries()->exists()) {

            session()->flash(
                'error',
                'حداقل یک تصویر برای محصول ثبت کنید'
            );

            return;
        }

        if (!$product->properties()->exists()) {

            session()->flash(
                'error',
                'حداقل یک ویژگی ثبت کنید'
            );

            return;
        }

        if (!$product->files()->exists()) {

            session()->flash(
                'error',
                'حداقل یک فایل آپلود کنید'
            );

            return;
        }

        $product->update([
            'status' => ProductStatus::PendingReview,
        ]);

        session()->flash(
            'message',
            'محصول با موفقیت برای بررسی ارسال شد'
        );
    }

    public function render()
    {

        $files = $this->product
            ->files()
            ->latest()
            ->get();

        return view(
            'livewire.admin.products.product-file-list',
            compact('files')
        );
    }
}
