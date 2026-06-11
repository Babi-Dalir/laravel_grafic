<?php

namespace App\Livewire\Admin\Transactions;

use App\Models\SellerWalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class SellerTransactionList extends Component
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
        $transactions = SellerWalletTransaction::query()
            ->with(['seller', 'order'])

            ->when($this->search, function ($q) {

                $q->where(function ($q) {

                    $q->whereHas('seller', function ($q) {
                        $q->where('brand_name', 'like', "%{$this->search}%")
                            ->orWhere('first_name', 'like', "%{$this->search}%")
                            ->orWhere('national_code', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    })

                        ->orWhereHas('order', function ($q) {
                            $q->where('order_code', 'like', "%{$this->search}%");
                        });

                });

            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.transactions.seller-transaction-list',compact('transactions'));
    }
}
