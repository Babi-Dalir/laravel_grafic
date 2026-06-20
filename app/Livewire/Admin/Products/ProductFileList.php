<?php

namespace App\Livewire\Admin\Products;

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

    // مشخص کردن قالب پجینیشن متناسب با بوت‌استرپ
    protected $paginationTheme = 'bootstrap';

    public function mount(Product $product)
    {
        $this->productId = $product->id;
    }

    protected ?Product $cachedProduct = null;

    public function getProductProperty()
    {
        return $this->cachedProduct ??= Product::with('files')->findOrFail($this->productId);
    }

    #[On('refresh-file-list')]
    public function refreshFileList()
    {
        $this->reset(['title']);
        // ریفرش کردن پجینیشن به صفحه اول برای دیدن فایل جدید الزامی است
        $this->resetPage();
        session()->flash('message', 'فایل با موفقیت آپلود و ذخیره شد.');
    }

    public function deleteFile($id)
    {
        try {
            $file = $this->product->files()->findOrFail($id);
            $wasDefault = $file->is_default;

            DB::transaction(function () use ($file, $wasDefault) {
                $service = app(ProductFileUploadService::class);
                $service->delete($file);

                if ($wasDefault) {
                    $nextDefault = $this->product->files()->where('id', '!=', $file->id)->first();
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

    // اصلاح ساختار ورودی برای هماهنگی کامل با Livewire 3 dispatch
    #[On('destroy_product_file')]
    public function destroyProductFile($fileId)
    {
        $this->deleteFile($fileId);
    }

    public function setDefault($id)
    {
        $file = $this->product->files()->findOrFail($id);
        DB::transaction(function () use ($file) {
            $this->product->files()->lockForUpdate()->update(['is_default' => false]);
            $file->update(['is_default' => true]);
        });

        session()->flash('message', 'فایل اصلی با موفقیت تغییر کرد.');
    }

    public function submitForReview()
    {
        $result = $this->product->submitForReview();

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

    public function render()
    {
        $files = $this->product->files()->latest()->paginate(10);

        return view('livewire.admin.products.product-file-list', [
            'files' => $files,
            'product' => $this->product
        ]);
    }
}
