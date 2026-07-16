<?php

namespace App\Livewire\Admin\Products;

use App\Enums\ProductStatus;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $productRequestId;
    public $review_note;

    protected $queryString = ['search' => ['except' => '']];

    public function approveProductRequest($id)
    {
        if (auth()->user()->hasRole('مدیر')) {
            $productRequest = Product::findOrFail($id);

            $productRequest->update([
                'status' => ProductStatus::Approved->value,
                'review_note' => null,
            ]);

            session()->flash('message', 'محصول با موفقیت تایید شد.');
        }
    }

    public function rejectProductRequest()
    {
        if (auth()->user()->hasRole('مدیر')) {
            $request = Product::findOrFail($this->productRequestId);

            $request->update([
                'status' => ProductStatus::Rejected->value,
                'review_note' => $this->review_note,
            ]);

            $this->reset(['productRequestId', 'review_note']);
            $this->dispatch('closeRejectModal');

            session()->flash('message', 'درخواست رد محصول با موفقیت ثبت شد.');
        }
    }

    #[On('destroy_product')]
    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $user = auth()->user();

        if (!$user->hasAnyRole(['مدیر', 'مدیر فروش']) && $product->user_id !== $user->id) {
            abort(403);
        }

        // اگر محصول فروخته شده باشد آرشیو می‌شود
        if ($product->orderDetails()->exists()) {
            $product->update([
                'status' => ProductStatus::Archived->value
            ]);
            $this->dispatch('productArchived', ['message' => 'محصول به دلیل داشتن تاریخچه فروش آرشیو شد.']);
            return;
        }

        $product->delete();
        $this->dispatch('productDeleted', ['message' => 'محصول با موفقیت به زباله‌دان منتقل شد.']);
    }

    public function changeStatus($id)
    {
        if (auth()->user()->hasRole('مدیر')) {
            $product = Product::findOrFail($id);

            // بازنویسی ایمن و تمیز سوییچ وضعیت‌ها بر اساس متد شما
            switch ($product->status) {
                case ProductStatus::Draft->value:
                    $product->update(['status' => ProductStatus::PendingReview->value]);
                    break;
                case ProductStatus::PendingReview->value:
                    $product->update(['status' => ProductStatus::Approved->value]);
                    break;
                case ProductStatus::Approved->value:
                    $product->update(['status' => ProductStatus::Rejected->value]);
                    break;
                case ProductStatus::Rejected->value:
                    $product->update(['status' => ProductStatus::Archived->value]);
                    break;
                case ProductStatus::Archived->value:
                    $product->update(['status' => ProductStatus::Draft->value]);
                    break;
            }

            session()->flash('message', 'وضعیت محصول با موفقیت تغییر کرد.');
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()->with(['files', 'user'])
            ->where('name', 'like', '%' . $this->search . '%');

        if (!auth()->user()->hasRole('مدیر')) {
            $query->where('user_id', auth()->id());
        }

        $products = $query->latest()->paginate(15);

        return view('livewire.admin.products.product-list', compact('products'));
    }
}
