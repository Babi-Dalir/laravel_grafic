<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Review;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductReviewList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public $product_id;
    #[On('destroy_review')]
    public function destroyReview($id)
    {
        Review::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $reviews = Review::query()
            ->where('product_id',$this->product_id)
            ->where('name','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.products.product-review-list',compact('reviews'));
    }
}
