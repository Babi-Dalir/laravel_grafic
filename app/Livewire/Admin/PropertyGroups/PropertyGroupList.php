<?php

namespace App\Livewire\Admin\PropertyGroups;

use App\Models\Product;
use App\Models\PropertyGroup;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyGroupList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_property_group')]
    public function destroyPropertyGroup($id)
    {
        PropertyGroup::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $property_groups = PropertyGroup::query()
            ->where('name','like','%'.$this->search.'%')
            ->paginate(20);
        return view('livewire.admin.property-groups.property-group-list',compact('property_groups'));
    }
}
