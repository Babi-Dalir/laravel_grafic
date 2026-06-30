<?php

namespace App\Livewire\Seller\SellerProducts;

use App\Enums\ProductStatus;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SellerProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = ''; // مقدار پیش‌فرض رشته‌ای برای جلوگیری از خطاهای احتمالی در بلید

    public $showRejectModal = false;
    public $rejectReason = null;

    /**
     * شنونده تغییرات فیلد سرچ جهت بازنشانی صفحه پیجینیشن
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('destroy_seller_product')]
    public function destroySellerProduct($id)
    {
        // اطمینان از مالکیت محصول توسط فروشنده احراز هویت شده
        $product = Product::query()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // اگر در سفارشات ثبت شده باشد -> تغییر وضعیت به غیرفعال/آرشیو
        if ($product->orderDetails()->exists()) {
            $product->update([
                'status' => ProductStatus::Archived
            ]);

            $this->dispatch('sellerProductArchived');
            return;
        }

        $product->delete();
        $this->dispatch('sellerProductDeleted');
    }

    public function showRejectReason($id)
    {
        $product = Product::query()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $this->rejectReason = $product->review_note;
        $this->showRejectModal = true;
    }

    public function render()
    {
        // 🟢 رفع کامل باگ N+1 با استفاده از Eager Loading و لایه محاسباتی بایند شده به دیتابیس
        $products = Product::query()
            ->where('user_id', auth()->id())
            ->with(['category:id,name']) // لود اتمیک نام دسته‌بندی
            ->withSum('downloads as total_download_count', 'download_count') // محاسبه مجموع دانلودها در یک کوئری مستقل
            ->where('name', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(10);

        return view('livewire.seller.seller-products.seller-product-list', compact('products'));
    }
}
