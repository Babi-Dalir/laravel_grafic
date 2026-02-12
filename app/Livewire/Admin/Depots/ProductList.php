<?php

namespace App\Livewire\Admin\Depots;

use App\Enums\DepotType;
use App\Models\DepotControl;
use App\Models\DepotProduct;
use App\Models\Product;
use App\Models\ProductPrice;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $depot_id;

    protected $paginationTheme = 'bootstrap';
    public $search, $search_depot;
    #[On('refreshDepotList')]
    public function refreshDepotList()
    {
        $this->dispatch('$refresh');
    }

    public function addDepot($product_price_id,$product_price_count,$depot_id)
    {
        DepotProduct::query()->create([
            'depot_id'=>$depot_id,
            'product_price_id'=>$product_price_id,
            'count'=>$product_price_count,
        ]);
        DepotControl::query()->create([
            'user_id'=>auth()->user()->id,
            'depot_id'=>$depot_id,
            'product_price_id'=>$product_price_id,
            'count'=>$product_price_count,
            'event_type'=>DepotType::AddDepot->value,
        ]);
        session()->flash('messageAdd','محصول با موفقیت به انبار اضافه شد');
    }

    public function deleteDepot($depot_product_id)
    {
        $depot_product = DepotProduct::query()->find($depot_product_id);
        DepotControl::query()->create([
            'user_id'=>auth()->user()->id,
            'depot_id'=>$depot_product->depot_id,
            'product_price_id'=>$depot_product->product_price_id,
            'count'=>$depot_product->count,
            'event_type'=>DepotType::DeleteDepot->value,
        ]);
        $depot_product->delete();
        session()->flash('messageDelete','محصول با موفقیت از انبار حذف شد');
    }
    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $depot_products = DepotProduct::query()
            ->where('depot_id', $this->depot_id)
            ->whereHas('productPrice', function ($q) {
                $q->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search_depot . '%');
                });
            })
            ->paginate(5);
        $exist_depot_product = DepotProduct::query()->select('product_price_id')->get()->toArray();
        $product_prices = ProductPrice::query()->whereNotIn('id',$exist_depot_product)
            ->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(5);
        return view('livewire.admin.depots.product-list', compact('product_prices','depot_products'));
    }
}
