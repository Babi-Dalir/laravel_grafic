<?php

namespace App\Livewire\Admin\Products;

use App\Helpers\ImageManager;
use App\Models\Gallery;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class GalleryList extends Component
{
    public $product;

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;

    #[On('destroy_gallery')]
    public function destroyGallery($id)
    {
        $gallery = Gallery::query()->find($id);
        ImageManager::unlinkImage('products',$gallery);
        Gallery::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $galleries = Gallery::query()
            ->where('product_id',$this->product->id)
            ->orderBy('position',"DESC")
            ->paginate(10);
        return view('livewire.admin.products.gallery-list',compact('galleries'));
    }
}
