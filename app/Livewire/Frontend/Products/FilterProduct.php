<?php

namespace App\Livewire\Frontend\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Guaranty;
use Livewire\Component;

class FilterProduct extends Component
{
    public $main_slug;
    public $sub_slug;
    public $child_slug;
    public $brands;
    public $guaranties;
    public $colors;
    public $filter_brand_list=[];
    public $filter_guaranty_list=[];
    public $filter_color_list=[];
    public function mount()
    {
        $products = Category::getProductByCategory($this->main_slug, $this->sub_slug, $this->child_slug, 'id', 'DESC', null);
        $brandList = [];
        $guarantyList = [];
        $colorList = [];
        foreach ($products as $product){
            if (!in_array($product->brand_id,$brandList)){
                array_push($brandList,$product->brand_id);
            }
        }
        foreach ($products as $product){
            foreach ($product->productPrices as $guaranty_product){
                if (!in_array($guaranty_product->guaranty_id,$guarantyList)){
                    array_push($guarantyList,$guaranty_product->guaranty_id);
                }
            }
        }
        foreach ($products as $product){
            foreach ($product->productPrices as $color_product){
                if (!in_array($color_product->color_id,$colorList)){
                    array_push($colorList,$color_product->color_id);
                }
            }
        }
        $this->brands = Brand::query()->whereIn('id',$brandList)->get();
        $this->guaranties = Guaranty::query()->whereIn('id',$guarantyList)->get();
        $this->colors = Color::query()->whereIn('id',$colorList)->get();
    }

    public function filterBrand($brand_id)
    {
        if (!in_array($brand_id,$this->filter_brand_list)){
            array_push($this->filter_brand_list,$brand_id);
        }else{
            if (($key=array_search($brand_id,$this->filter_brand_list)) !== false){
                unset($this->filter_brand_list[$key]);
            }
        }
        $this->dispatch('filterProducts',$this->filter_brand_list,$this->filter_guaranty_list,$this->filter_color_list);
    }
    public function filterGuaranty($guaranty_id)
    {
        if (!in_array($guaranty_id,$this->filter_guaranty_list)){
            array_push($this->filter_guaranty_list,$guaranty_id);
        }else{
            if (($key=array_search($guaranty_id,$this->filter_guaranty_list)) !== false){
                unset($this->filter_guaranty_list[$key]);
            }
        }
        $this->dispatch('filterProducts',$this->filter_brand_list,$this->filter_guaranty_list,$this->filter_color_list);
    }
    public function filterColor($color_id)
    {
        if (!in_array($color_id,$this->filter_color_list)){
            array_push($this->filter_color_list,$color_id);
        }else{
            if (($key=array_search($color_id,$this->filter_color_list)) !== false){
                unset($this->filter_color_list[$key]);
            }
        }
        $this->dispatch('filterProducts',$this->filter_brand_list,$this->filter_guaranty_list,$this->filter_color_list);
    }
    public function render()
    {
        return view('livewire.frontend.products.filter-product');
    }
}
