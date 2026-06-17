<?php

namespace App\Livewire\Seller\SellerProducts;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductPrice;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SellerProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search;

    public $showRejectModal = false;
    public $rejectReason = null;


    #[On('destroy_seller_product')]
    public function destroySellerProduct($id)
    {
        $product = Product::query()->where('user_id', auth()->user()->id)
            ->findOrFail($id);

        // اگر در سفارش استفاده شده → حذف نکن، آرشیو کن
        if ($product->orderDetails()->exists()) {

            $product->update([
                'status' => ProductStatus::Archived->value
            ]);

            $this->dispatch('sellerProductArchived');

            return;
        }

        $product->delete();

        $this->dispatch('sellerProductDeleted');
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function showRejectReason($id)
    {
        $product = Product::query()->where('user_id', auth()->user()->id)
            ->findOrFail($id);

        $this->rejectReason = $product->review_note;
        $this->showRejectModal = true;
    }


    public function render()
    {
        $products = Product::query()
            ->where('user_id', auth()->user()->id)
            ->where('name', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(10);

        return view('livewire.seller.seller-products.seller-product-list', compact('products'));
    }
}
