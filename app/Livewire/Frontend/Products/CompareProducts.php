<?php

namespace App\Livewire\Frontend\Products;

use App\Models\Product;
use App\Models\PropertyGroup;
use Livewire\Component;

class CompareProducts extends Component
{
    public $product_id_1;
    public $product_id_2;
    public $productList=[];
    public $products;
    public $property_groups;

    public function mount()
    {
        array_push($this->productList,$this->product_id_1);
        array_push($this->productList,$this->product_id_2);

        $this->products = Product::query()->whereIn('id',$this->productList)->get();

        $product = Product::query()->find($this->product_id_1);
        $category_id = $product->category->parentCategory->id;
        $this->property_groups = PropertyGroup::query()->where('category_id',$category_id)->get();
    }
    public function render()
    {
        return view('livewire.frontend.products.compare-products');
    }
}
