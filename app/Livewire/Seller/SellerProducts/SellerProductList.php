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

    #[On('destroy_product_price')]
    public function destroyProductPrice($id)
    {
        $product = Product::find($id);
        $product->delete();
    }
    public function changeStatus($id)
    {
        $product = Product::query()->find($id);
        if ($product->status == ProductStatus::Waiting->value){
            $product->update([
                'status'=>ProductStatus::Active->value
            ]);
        }elseif ($product->status == ProductStatus::Active->value){
            $product->update([
                'status'=>ProductStatus::InActive->value
            ]);
        }elseif ($product->status == ProductStatus::InActive->value){
            $product->update([
                'status'=>ProductStatus::Draft->value
            ]);
        }elseif ($product->status == ProductStatus::Draft->value){
            $product->update([
                'status'=>ProductStatus::Rejected->value
            ]);
        }elseif ($product->status == ProductStatus::Rejected->value){
            $product->update([
                'status'=>ProductStatus::Waiting->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::query()
            ->where('user_id',auth()->user()->id)
            ->where('name','like','%'.$this->search.'%')
            ->latest()
            ->paginate(10);
        return view('livewire.seller.seller-products.seller-product-list',compact('products'));
    }
}
