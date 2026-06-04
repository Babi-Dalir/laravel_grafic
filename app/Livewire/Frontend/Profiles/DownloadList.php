<?php

namespace App\Livewire\Frontend\Profiles;

use App\Models\Downloads;
use Livewire\Component;
use Livewire\WithPagination;

class DownloadList extends Component
{
    use WithPagination;

    public function render()
    {
        $downloads = Downloads::query()
            ->where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(1);
        return view('livewire.frontend.profiles.download-list',compact('downloads'));
    }
}
