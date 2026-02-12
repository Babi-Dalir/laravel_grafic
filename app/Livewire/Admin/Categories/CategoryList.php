<?php

namespace App\Livewire\Admin\Categories;

use App\Enums\UserStatus;
use App\Models\Category;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class CategoryList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public $categories;
    public $search_categories;

    public function mount()
    {
        $this->categories = Category::query()
            ->where('parent_id',0)
            ->get();
    }

    public function updatingSearch($value)
    {
        $this->search_categories = Category::query()
            ->where('name','like','%'.$value.'%')
            ->get();
    }
    #[On('destroy_category')]
    public function destroyCategory($id)
    {
        Category::destroy($id);
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
