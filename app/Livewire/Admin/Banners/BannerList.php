<?php

namespace App\Livewire\Admin\Banners;

use App\Helpers\ImageManager;
use App\Models\Banner;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BannerList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    /**
     * هوک لایووایر برای ریست کردن اتوماتیک پجینیشن در زمان تغییر سرچ (بدون نیاز به دکمه اضافه)
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('destroy_banner')]
    public function destroyBanner($id)
    {
        $banner = Banner::findOrFail($id);

        // حذف عکس و دیتابیس
        ImageManager::unlinkImage('banners', $banner);
        $banner->delete();

        // 🟢 حل باگ: پاکسازی کش صفحه اصلی پس از حذف فیزیکی بنر
        Banner::clearCache();
    }

    public function render()
    {
        $banners = Banner::query()
            ->when($this->search, function ($query) {
                $query->where('type', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.banners.banner-list', compact('banners'));
    }
}
