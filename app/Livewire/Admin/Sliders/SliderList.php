<?php

namespace App\Livewire\Admin\Sliders;

use App\Enums\SliderStatus;
use App\Helpers\ImageManager;
use App\Models\Slider;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SliderList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('destroy_slider')]
    public function destroySlider($id)
    {
        $slider = Slider::findOrFail($id);

        ImageManager::unlinkImage('sliders', $slider);
        $slider->delete();

        // 🟢 حل باگ کش پس از حذف اسلایدر
        Slider::clearCache();
    }

    /**
     * سوئیچ وضعیت اسلایدر (Toggle Status) به صورت کاملاً بهینه و تمیز
     */
    public function changeStatus($id)
    {
        $slider = Slider::findOrFail($id);

        // تشخیص وضعیت جدید بر اساس وضعیت فعلی آبجکت انوم
        $newStatus = $slider->status === SliderStatus::Active ? SliderStatus::InActive : SliderStatus::Active;

        $slider->update(['status' => $newStatus]);

        // 🟢 حل باگ کش پس از تغییر وضعیت اسلایدر
        Slider::clearCache();
    }

    public function render()
    {
        $sliders = Slider::query()
            ->when($this->search, function ($query) {
                $query->where('link', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.sliders.slider-list', compact('sliders'));
    }
}
