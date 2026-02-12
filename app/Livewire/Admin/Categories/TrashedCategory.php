<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TrashedCategory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_trash_category')]
    public function destroyCategory($id)
    {
        Category::query()->withTrashed()->find($id)->forceDelete();
    }

    public function restoreCategory($id)
    {
        Category::query()->withTrashed()->find($id)->restore();
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $categories = Category::query()->onlyTrashed()
            ->where('name','like','%'.$this->search.'%')
            ->orWhere('e_name','like','%'.$this->search.'%')
            ->where('deleted_at','!=',null)
            ->paginate(10);
        return view('livewire.admin.categories.trashed-category',compact('categories'));
    }
}
