<?php

namespace App\Livewire\Admin\Colors;

use App\Models\Brand;
use App\Models\Color;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ColorList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_color')]
    public function destroyColor($id)
    {
        Color::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $colors = Color::query()
            ->where('name','like','%'.$this->search.'%')
            ->Orwhere('code','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.colors.color-list',compact('colors'));
    }
}
