<?php

namespace App\Livewire\Admin\Sliders;

use App\Models\Slider;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SliderList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_slider')]
    public function destroySlider($id)
    {
        Slider::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $sliders = Slider::query()->paginate(10);
        return view('livewire.admin.sliders.slider-list',compact('sliders'));
    }
}
