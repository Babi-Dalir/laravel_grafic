<?php

namespace App\Models; // در صورت نیاز به ایمپورت متدهای مدل

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TrashedCategory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('destroy_trash_category')]
    public function destroyCategory($id)
    {
        $category = Category::query()->withTrashed()->find($id);
        if ($category) {
            $category->forceDelete();
        }
    }

    public function restoreCategory($id)
    {
        $category = Category::query()->withTrashed()->find($id);
        if ($category) {
            $category->restore();
        }
        Cache::forget('categories');
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::query()
            ->onlyTrashed()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('e_name', 'like', '%' . $this->search . '%');
            })
            ->paginate(15);

        return view('livewire.admin.categories.trashed-category', compact('categories'));
    }
}
