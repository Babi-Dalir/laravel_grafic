<?php

namespace App\Livewire\Admin\Guaranties;

use App\Models\Guaranty;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class GuarantyList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_guaranty')]
    public function destroyGuaranty($id)
    {
        Guaranty::destroy($id);
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $guaranties = Guaranty::query()
            ->where('name','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.guaranties.guaranty-list',compact('guaranties'));
    }
}
