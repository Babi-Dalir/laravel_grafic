<?php

namespace App\Livewire\Frontend\Profiles;

use App\Enums\ProductStatus;
use App\Models\Favorite;
use Livewire\Component;
use Livewire\WithPagination;

class FavoriteList extends Component
{
    use WithPagination;

    public function deleteFavorite($product_id)
    {
        Favorite::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $product_id)
            ->delete();

        // 🟢 واکسینه کردن پاژینیشن در صورت خالی شدن صفحه جاری پس از حذف
        $favorites = Favorite::query()
            ->where('user_id', auth()->id())
            ->whereHas('product', function ($query) {
                $query->where('status', '!=', ProductStatus::Archived->value);
            })
            ->paginate(15);

        if ($this->getPage() > $favorites->lastPage()) {
            $this->setPage(max($favorites->lastPage(), 1));
        }
    }

    public function render()
    {
        $favorites = Favorite::query()
            ->with('product')
            ->where('user_id', auth()->id())
            ->whereHas('product', function ($query) {
                $query->where('status', '!=', ProductStatus::Archived->value);
            })
            ->paginate(15);

        return view('livewire.frontend.profiles.favorite-list', compact('favorites'));
    }
}
