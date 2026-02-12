<?php

namespace App\Livewire\Admin\Provinces;

use App\Models\Province;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProvinceList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_province')]
    public function destroyProvince($id)
    {
        Province::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $provinces = Province::query()
            ->where('province','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.provinces.province-list',compact('provinces'));
    }
}
