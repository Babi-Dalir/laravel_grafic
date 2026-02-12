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
    public function destroyProductPrice($product_price_id)
    {
        $product_price = ProductPrice::query()->find($product_price_id);
        $product_id = $product_price->product_id;
        $product_price->delete();
        $less_price = ProductPrice::query()->orderBy('price', "ASC")
            ->where('product_id', $product_id)->first();

        $product = Product::query()->find($product_id);
        if ($less_price) {
            $product->update([
                'price' => $less_price->price,
                'discount' => $less_price->discount,
                'count' => $less_price->count,
                'max_sell' => $less_price->max_sell,
                'guaranty_id' => $less_price->guaranty_id,
                'is_spacial' => $less_price->is_spacial !=null ? $less_price->is_spacial : false,
                'special_expiration' => $less_price->special_expiration,
            ]);
        } else {
            $product->update([
                'price' => 0,
                'discount' => 0,
                'count' => 0,
                'max_sell' => null,
                'guaranty_id' => null,
                'is_spacial' => false,
                'special_expiration' => null,
            ]);
        }
        $colors = [];
        if ($less_price){
            $product_prices = ProductPrice::query()
                ->where('product_id', $product_id)
                ->where('guaranty_id', $less_price->guaranty_id)->get();
            foreach ($product_prices as $product_price) {
                array_push($colors, $product_price->color_id);
            }
            $product->colors()->sync($colors);
        }
    }
    public function changeStatus($product_price_id)
    {
        $product_price = ProductPrice::query()->find($product_price_id);
        if ($product_price->status == ProductStatus::Waiting->value){
            $product_price->update([
                'status'=>ProductStatus::Active->value
            ]);
        }elseif ($product_price->status == ProductStatus::Active->value){
            $product_price->update([
                'status'=>ProductStatus::InActive->value
            ]);
        }elseif ($product_price->status == ProductStatus::InActive->value){
            $product_price->update([
                'status'=>ProductStatus::StopProduction->value
            ]);
        }elseif ($product_price->status == ProductStatus::StopProduction->value){
            $product_price->update([
                'status'=>ProductStatus::Rejected->value
            ]);
        }elseif ($product_price->status == ProductStatus::Rejected->value){
            $product_price->update([
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
        $product_prices = ProductPrice::query()
            ->where('user_id',auth()->user()->id)
            ->paginate(10);
        return view('livewire.seller.seller-products.seller-product-list',compact('product_prices'));
    }
}
