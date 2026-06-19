<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\ProductFile;
use App\Services\ProductFileUploadService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFileList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public $productId;
    public $title;

    public function mount(Product $product)
    {
        $this->productId = $product->id;
    }

    /**
     * 🌟 متد کمکی ماژولار (Computed Property) برای دسترسی زنده به محصول جاری
     */
    public function getProductProperty()
    {
        return Product::findOrFail($this->productId);
    }

    public function handleChunkUpload(
        string $fileUuid,
        int $chunkIndex,
        int $totalChunks,
        string $originalName,
        string $base64Data
    ) {
        // ۱. بازیابی مستقیم محصول از دیتابیس بر اساس شناسه کامپوننت
        $product = \App\Models\Product::findOrFail($this->productId);

        // ۲. دریافت کاربر جاری به همراه بررسی وجود سشن معتبر
        $user = auth()->user();
        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'نشست کاربری شما منقضی شده است. لطفا صفحه را ریفرش کرده و مجددا وارد شوید.'
            ];
        }

        // ۳. 🔒 لایه امنیتی مستقیم و نفوذناپذیر بر اساس ستون user_id مایگریشن
        if (!$user->hasRole('مدیر')) {
            // تبدیل هر دو شناسه به عدد صحیح (int) برای جلوگیری از خطای عدم تطابق نوع داده (Type Mismatch)
            $productOwnerId = (int) $product->user_id;
            $currentUserId = (int) $user->id;

            if ($productOwnerId !== $currentUserId) {
                return [
                    'status' => 'error',
                    'message' => 'شما دسترسی امنیتی لازم برای آپلود فایل روی این محصول را ندارید.'
                ];
            }
        }

        try {
            $service = app(ProductFileUploadService::class);

            $productFile = $service->uploadChunk(
                $base64Data,
                $fileUuid,
                $chunkIndex,
                $totalChunks,
                $originalName,
                $product,
                $this->title
            );

            if ($productFile !== null) {
                $this->reset(['title']);
                session()->flash('message', 'فایل با موفقیت آپلود، از نظر امنیتی اسکن و در سرور ذخیره شد.');
            }

            return ['status' => 'success'];

        } catch (\Throwable $e) {
            logger()->error("خطا در آپلود چانک محصول: " . $e->getMessage(), [
                'product_id' => $this->productId,
                'exception' => get_class($e)
            ]);

            $friendlyMessage = 'خطای ناشناخته‌ای در پردازش فایل رخ داد. لطفا دوباره تلاش کنید.';
            $errorText = $e->getMessage();

            if (str_contains($errorText, 'Failed to open stream') || str_contains($errorText, 'stat failed')) {
                $friendlyMessage = 'سرور در حال حاضر قادر به خواندن یا ذخیره این تکه از فایل نیست.';
            } elseif (str_contains($errorText, 'Permission denied') || str_contains($errorText, 'mkdir()')) {
                $friendlyMessage = 'خطای دسترسی سرور: اجازه ساخت پوشه داده نشد.';
            } elseif (str_contains($errorText, 'base64_decode')) {
                $friendlyMessage = 'داده‌های ارسالی مخدوش شده‌اند.';
            } elseif ($e instanceof \Exception) {
                $friendlyMessage = $errorText;
            }

            return ['status' => 'error', 'message' => $friendlyMessage];
        }
    }
    /**
     * حذف ایمن فایل محصول با استفاده از اکشن کامپیوتر جدید دیتابیس
     */
    public function deleteFile($id)
    {
        try {
            // 🔒 اصلاح امنیت ۲: دسترسی از طریق ریلیشن زنده و جلوگیری کامل از خطر حملات IDOR
            $file = $this->product->files()->findOrFail($id);

            $service = app(ProductFileUploadService::class);
            $service->delete($file);

            session()->flash('message', 'فایل مورد نظر با موفقیت حذف شد.');
        } catch (\Throwable $e) {
            session()->flash('error', 'خطا در حذف فایل یا عدم دسترسی به منبع مشخص شده.');
        }
    }

    #[On('destroy_product_file')]
    public function destroyProductFile($fileId)
    {
        $this->deleteFile($fileId);
    }

    public function setDefault($id)
    {
        // 🔒 اصلاح امنیت ۳: استفاده از ریلیشن معتبر زنده کامپوننت برای هندل ایمن فایل اصلی
        $file = $this->product->files()->findOrFail($id);

        $this->product->files()->where('is_default', true)->update(['is_default' => false]);
        $file->update(['is_default' => true]);
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

        session()->flash('message', 'محصول شما با موفقیت ثبت شد و پس از بررسی مدیر منتشر خواهد شد.');
        return $this->redirect(route('seller.product.list'));
    }

    public function render()
    {
        // دریافت فایل‌های مربوط به محصول جاری
        $files = $this->product
            ->files()
            ->latest()
            ->paginate(10);

        // 🌟 ارسال مستقیم خود شیء محصول به قالب بلید برای حل خطای Undefined variable
        return view('livewire.admin.products.product-file-list', [
            'files' => $files,
            'product' => $this->product // این خط متغیر $product را در بلید می‌سازد
        ]);
    }
}
