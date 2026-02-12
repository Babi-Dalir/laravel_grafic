<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TrashedProduct extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_trash_product')]
    public function destroyProduct($id)
    {
        Product::query()->withTrashed()->find($id)->forceDelete();
    }

    public function restoreProduct($id)
    {
        Product::query()->withTrashed()->find($id)->restore();
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $products = Product::query()->onlyTrashed()
            ->where('name','like','%'.$this->search.'%')
            ->orWhere('e_name','like','%'.$this->search.'%')
            ->where('deleted_at','!=',null)
            ->paginate(10);
        return view('livewire.admin.products.trashed-product',compact('products'));
    }
}
