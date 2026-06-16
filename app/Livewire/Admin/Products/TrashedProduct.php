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
        $product = Product::withTrashed()
            ->findOrFail($id);

        // محصول دارای سابقه خرید است
        if ($product->orderDetails()->exists()) {

            $this->dispatch('productArchivedTrash');

            return;
        }

        $product->forceDelete();

        $this->dispatch('productDeletedTrash');
    }

    public function restoreProduct($id)
    {
        Product::query()->withTrashed()->find($id)->restore();
        session()->flash(
            'message',
            'محصول با موفقیت بازگردانی شد.'
        );
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $products = Product::onlyTrashed()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('e_name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.admin.products.trashed-product',compact('products'));
    }
}
