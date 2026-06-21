<?php

namespace App\Livewire\Admin\Products;

use App\Enums\UploadFileStatus;
use App\Models\Product;
use App\Models\ProductFile;
use App\Services\ProductFileUploadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFileList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public $productId;
    public $title;

    protected $paginationTheme = 'bootstrap';

    public bool $isProcessing = false; // 👈 پرچم هوشمند کنترل پولینگ

    public function mount(Product $product)
    {
        $this->productId = $product->id;
    }

    public function getProductProperty()
    {
        return Product::findOrFail($this->productId);
    }

    #[On('refresh-file-list')]
    public function refreshFileList()
    {
        $this->reset(['title']);
        $this->resetPage();
        $this->isProcessing = true; // فعال‌سازی موقت پولینگ UI
        session()->flash('message', 'فایل با موفقیت ارسال شد و در حال پردازش امنیتی است...');
    }

    public function deleteFile($id)
    {
        try {
            $file = ProductFile::where('product_id', $this->productId)->findOrFail($id);
            $wasDefault = $file->is_default;

            DB::transaction(function () use ($file, $wasDefault) {
                $service = app(ProductFileUploadService::class);
                $service->delete($file);

                if ($wasDefault) {
                    $nextDefault = ProductFile::where('product_id', $this->productId)->first();
                    if ($nextDefault) {
                        $nextDefault->update(['is_default' => true]);
                    }
                }
            });

            session()->flash('message', 'فایل مورد نظر با موفقیت حذف شد.');
            $this->dispatch('swal-success', title: 'فایل با موفقیت حذف شد.');

        } catch (\Throwable $e) {
            session()->flash('error', 'خطا در حذف فایل رخ داده است.');
        }
    }

    #[On('destroy_product_file')]
    public function destroyProductFile($fileId)
    {
        $this->deleteFile($fileId);
    }

    public function setDefault($id)
    {
        $file = ProductFile::where('product_id', $this->productId)->findOrFail($id);
        DB::transaction(function () use ($file) {
            ProductFile::where('product_id', $this->productId)->update(['is_default' => false]);
            $file->update(['is_default' => true]);
        });

        session()->flash('message', 'فایل اصلی با موفقیت تغییر کرد.');
    }

    public function submitForReview()
    {
        $product = $this->product;
        $result = $product->submitForReview();

        if ($result !== true) {
            foreach ($result as $field => $message) {
                $this->addError($field, $message);
            }
            return;
        }

        if (auth()->user()->hasRole('مدیر')) {
            session()->flash('message', 'محصول با موفقیت تایید و منتشر شد.');
            return $this->redirect(route('products.index'));
        }

        session()->flash('message', 'محصول شما ثبت شد و پس از بررسی منتشر خواهد شد.');
        return $this->redirect(route('seller.product.list'));
    }

    public function checkProcessingStatus()
    {
        if (!$this->isProcessing) {
            return;
        }

        $hasPending = ProductFile::where('product_id', $this->productId)
            ->whereIn('status', [UploadFileStatus::Uploading->value, UploadFileStatus::Processing->value])
            ->exists();

        // به محض تمام شدن پردازش‌ها، پرچم را خاموش کن تا پولینگ در قالب بلید متوقف شود
        if (!$hasPending) {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $files = ProductFile::where('product_id', $this->productId)->latest()->paginate(10);

        return view('livewire.admin.products.product-file-list', [
            'files' => $files,
            'product' => $this->product
        ]);
    }
}
