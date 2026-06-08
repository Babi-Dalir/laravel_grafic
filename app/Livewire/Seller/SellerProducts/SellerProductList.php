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

    #[On('destroy_seller_product')]
    public function destroySellerProduct($id)
    {
        $seller_product = Product::find($id);
        $seller_product->delete();
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::query()
            ->where('user_id', auth()->id())
            ->where('name', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(10);

        return view('livewire.seller.seller-products.seller-product-list', compact('products'));
    }
}
