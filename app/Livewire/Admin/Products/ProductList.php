<?php

namespace App\Livewire\Admin\Products;

use App\Enums\ProductStatus;
use App\Enums\UserStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_product')]
    public function destroyProduct($id)
    {
        $product = Product::find($id);
        $product->delete();
    }
    public function changeStatus($id)
    {
        $product = Product::query()->find($id);
        if ($product->status == ProductStatus::Draft->value){
            $product->update([
                'status'=>ProductStatus::PendingReview->value
            ]);
        }elseif ($product->status == ProductStatus::PendingReview->value){
            $product->update([
                'status'=>ProductStatus::Approved->value
            ]);
        }elseif ($product->status == ProductStatus::Approved->value){
            $product->update([
                'status'=>ProductStatus::Incomplete->value
            ]);
        }elseif ($product->status == ProductStatus::Incomplete->value){
            $product->update([
                'status'=>ProductStatus::Rejected->value
            ]);
        }elseif ($product->status == ProductStatus::Rejected->value){
            $product->update([
                'status'=>ProductStatus::Archived->value
            ]);
        }elseif ($product->status == ProductStatus::Archived->value){
            $product->update([
                'status'=>ProductStatus::Draft->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
//        if (auth()->user()->is_admin){
//            $products = Product::query()
//                ->where('name','like','%'.$this->search.'%')
//                ->paginate(10);
//        }
//        else{
            $products = Product::query()
//                ->where('status',ProductStatus::Active->value)
                ->where('name','like','%'.$this->search.'%')
                ->paginate(10);
//        }

        return view('livewire.admin.products.product-list',compact('products'));
    }
}
