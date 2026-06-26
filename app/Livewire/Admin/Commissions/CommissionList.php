<?php

namespace App\Livewire\Admin\Commissions;

use App\Models\Commission;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // فیلتر جستجو
    public $search = '';

    /**
     * 🟢 بهینه‌سازی استیت: ریست کردن صفحه در صورت تغییر متن جستجو
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Commission::query()->with('category');

        if (!empty(trim($this->search))) {
            $query->whereHas('category', function ($q) {
                $q->where('name', 'like', '%' . trim($this->search) . '%');
            });
        }

        $commissions = $query->latest()->paginate(20);

        return view('livewire.admin.commissions.commission-list', compact('commissions'));
    }
}
