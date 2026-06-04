<?php

namespace App\Livewire\Frontend\Profiles;

use App\Models\Favorite;
use Livewire\Component;
use Livewire\WithPagination;

class FavoriteList extends Component
{
    use WithPagination;
    public function deleteFavorite($product_id)
    {
        $favorite = Favorite::query()
            ->where('user_id',auth()->user()->id)
            ->where('product_id',$product_id)
            ->first();
        $favorite->delete();
    }
    public function render()
    {
        $favorites = Favorite::query()->where('user_id',auth()->user()->id)->paginate(1);
        return view('livewire.frontend.profiles.favorite-list',compact('favorites'));
    }
}
