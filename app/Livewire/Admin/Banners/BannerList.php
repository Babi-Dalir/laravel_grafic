<?php

namespace App\Livewire\Admin\Banners;

use App\Models\Banner;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BannerList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_banner')]
    public function destroyBanner($id)
    {
        Banner::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $banners = Banner::query()
            ->where('type','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.banners.banner-list',compact('banners'));
    }
}
