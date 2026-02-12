<?php

namespace App\Livewire\Frontend\Products;

use App\Models\ProductStar;
use Livewire\Attributes\On;
use Livewire\Component;

class StarProduct extends Component
{
    public $product;
    public $scoreList=[];
    #[On('getScore')]
    public function getScore($starValue,$id)
    {
        $this->scoreList[$id] = $starValue;
        $this->dispatch('sendScore',$this->scoreList);
    }
    public function render()
    {
        $stars = ProductStar::query()->where('product_id',$this->product->id)->get();
        return view('livewire.frontend.products.star-product',compact('stars'));
    }
}
