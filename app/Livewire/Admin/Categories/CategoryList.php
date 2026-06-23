<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $categories;
    public $search_categories = null;

    public function mount()
    {
        $this->categories = Category::query()
            ->with('childCategory.childCategory')
            ->where('parent_id', 0)
            ->get();
    }

    public function updatedSearch($value)
    {
        if (!empty($value)) {
            $this->search_categories = Category::query()
                ->where('name', 'like', '%' . $value . '%')
                ->get();
        } else {
            $this->search_categories = null;
        }
    }

    #[On('destroy_category')]
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        $this->mount(); // بروزرسانی لیست
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.categories.category-list');
    }
}
