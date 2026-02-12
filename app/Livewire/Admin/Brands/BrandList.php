<?php

namespace App\Livewire\Admin\Brands;

use App\Models\Brand;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BrandList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_brand')]
    public function destroyBrand($id)
    {
        Brand::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $brands = Brand::query()
            ->where('name','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.brands.brand-list',compact('brands'));
    }
}
