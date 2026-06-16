<?php

namespace App\Livewire\Admin\Products;

use App\Enums\ProductStatus;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public $productRequestId;
    public $review_note;

    public function approveProductRequest($id)
    {
        if (auth()->user()->hasRole('مدیر')) {
            $productRequest = Product::with('user')->findOrFail($id);

            $productRequest->update([
                'status' => ProductStatus::Approved->value,
                'review_note' => null,
            ]);

            session()->flash(
                'message',
                'درخواست فروشندگی با موفقیت تایید شد.'
            );
        }
    }

    public function rejectProductRequest()
    {
        if (auth()->user()->hasRole('مدیر')) {
            $request = Product::findOrFail($this->productRequestId);

            $request->update([
                'status' => ProductStatus::Rejected->value,
                'review_note' => $this->review_note,
            ]);

            $this->reset([
                'productRequestId',
                'review_note'
            ]);

            $this->dispatch('closeRejectModal');

            session()->flash(
                'message',
                'درخواست با موفقیت رد شد.'
            );
        }
    }
    #[On('destroy_product')]
    public function destroyProduct($id)
    {
        $product = Product::query()->find($id);
        $product->delete();

//        $product = Product::findOrFail($id);
//
//        $user = auth()->user();
//
//        if (
//            !$user->hasAnyRole(['مدیر', 'مدیر فروش']) &&
//            $product->user_id !== $user->id
//        ) {
//            abort(403);
//        }
//
//        $product->delete();

    }
    public function changeStatus($id)
    {
        if (auth()->user()->hasRole('مدیر')) {
            $product = Product::query()->find($id);
            if ($product->status == ProductStatus::Draft->value){
                $product->update([
                    'status'=>ProductStatus::PendingReview->value
                ]);
            }elseif ($product->status == ProductStatus::PendingReview->value){
                $product->update([
                    'status'=>ProductStatus::Approved->value
                ]);
            }elseif ($product->status == ProductStatus::Approved->value){
                $product->update([
                    'status'=>ProductStatus::Rejected->value
                ]);
            }elseif ($product->status == ProductStatus::Rejected->value){
                $product->update([
                    'status'=>ProductStatus::Archived->value
                ]);
            }elseif ($product->status == ProductStatus::Archived->value){
                $product->update([
                    'status'=>ProductStatus::Draft->value
                ]);
            }
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $query = Product::query()
            ->where('name', 'like', '%' . $this->search . '%');

        if (!auth()->user()->hasRole('مدیر')) {
            $query->where('user_id', auth()->id());
        }

        $products = $query->paginate(10);

        return view('livewire.admin.products.product-list', compact('products'));
    }
}
