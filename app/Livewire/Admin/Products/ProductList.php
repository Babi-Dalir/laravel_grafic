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
        Product::destroy($id);
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
                'status'=>ProductStatus::StopProduction->value
            ]);
        }elseif ($product->status == ProductStatus::StopProduction->value){
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
        if (auth()->user()->is_admin){
            $products = Product::query()
                ->where('name','like','%'.$this->search.'%')
                ->paginate(10);
        }else{
            $products = Product::query()
                ->where('status',ProductStatus::Active->value)
                ->where('name','like','%'.$this->search.'%')
                ->paginate(10);
        }

        return view('livewire.admin.products.product-list',compact('products'));
    }
}
