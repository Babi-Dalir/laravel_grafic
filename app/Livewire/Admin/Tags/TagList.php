<?php

namespace App\Livewire\Admin\Tags;

use App\Models\Tag;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TagList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_tag')]
    public function destroyTag($id)
    {
        Tag::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $tags = Tag::query()
            ->where('name','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.tags.tag-list',compact('tags'));
    }
}
