<?php

namespace App\Livewire\Admin\Commissions;

use App\Models\Commission;
use App\Models\PropertyGroup;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search;

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $commissions = Commission::query()
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(20);
        return view('livewire.admin.commissions.commission-list', compact('commissions'));
    }
}
