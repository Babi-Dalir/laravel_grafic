<?php

namespace App\Livewire\Admin\Cities;

use App\Models\Category;
use App\Models\City;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CityList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_city')]
    public function destroyCity($id)
    {
        City::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $cities = City::query()
            ->where('city','like','%'.$this->search.'%')
            ->paginate(20);
        return view('livewire.admin.cities.city-list',compact('cities'));
    }
}
