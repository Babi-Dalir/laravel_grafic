<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TrashedProduct extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function searchData()
    {
        $this->resetPage();
    }

    #[On('destroy_trash_product')]
    public function destroyProduct($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $user = auth()->user();


        if (!$user->hasAnyRole(['مدیر', 'مدیر فروش']) && $product->user_id !== $user->id) {
            abort(403);
        }

        if ($product->orderDetails()->exists()) {
            $this->dispatch('productArchivedTrash');
            return;
        }

        $product->forceDelete();
        $this->dispatch('productDeletedTrash');
    }

    public function restoreProduct($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $user = auth()->user();

        if (!$user->hasAnyRole(['مدیر', 'مدیر فروش']) && $product->user_id !== $user->id) {
            abort(403);
        }

        $product->restore();
        session()->flash('message', 'محصول با موفقیت بازگردانی شد.');
    }

    public function render()
    {
        $query = Product::onlyTrashed()->with(['category', 'user'])
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('e_name', 'like', '%' . $this->search . '%');
            });

        if (!auth()->user()->hasRole('مدیر')) {
            $query->where('user_id', auth()->id());
        }

        $products = $query->latest('deleted_at')->paginate(15);

        return view('livewire.admin.products.trashed-product', compact('products'));
    }
}
