<?php

namespace App\Livewire\Seller\Settlements;

use App\Models\SellerSettlement;
use App\Services\SettlementManager;
use Livewire\Component;
use Livewire\WithPagination;

class SettlementList extends Component
{
    public $search;

    use WithPagination;

    public function searchData()
    {
        $this->resetPage();
    }

    public function pay($settlementId)
    {
        $settlement = SellerSettlement::findOrFail($settlementId);

        SettlementManager::markAsPaid(
            $settlement,
            auth()->id()
        );

        session()->flash('success', 'تسویه با موفقیت ثبت شد');
    }

    public function render()
    {
        $settlements = SellerSettlement::query()
            ->with(['seller', 'user'])
            ->when($this->search, function ($q) {
                $q->whereHas('seller', function ($q) {
                    $q->where('brand_name', 'like', "%{$this->search}%")
                        ->orWhere('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.seller.settlements.settlement-list', [
            'settlements' => $settlements,
        ]);
    }
}
