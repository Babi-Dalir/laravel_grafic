<?php

namespace App\Livewire\Admin\Sliders;

use App\Enums\SliderStatus;
use App\Enums\UserStatus;
use App\Helpers\ImageManager;
use App\Models\Slider;
use App\Models\User;
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
        $slider = Slider::findOrFail($id);
        ImageManager::unlinkImage('sliders', $slider);
        $slider->delete();
    }

    public function changeStatus($id)
    {
        $slider = Slider::query()->find($id);
        if ($slider->status == SliderStatus::Active->value) {
            $slider->update([
                'status' => SliderStatus::InActive->value
            ]);
        } elseif ($slider->status == SliderStatus::InActive->value) {
            $slider->update([
                'status' => SliderStatus::Active->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sliders = Slider::query()->paginate(10);
        return view('livewire.admin.sliders.slider-list', compact('sliders'));
    }
}
