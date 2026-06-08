<?php

namespace App\Livewire\Seller\Wallet;

use App\Enums\TransactionType;
use App\Models\SellerWalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class SellerWalletTransactionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $type = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $seller = auth()->user()->seller;

        $transactions = SellerWalletTransaction::query()
            ->where('seller_id', $seller->id)
            ->when($this->search, function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->type, function ($q) {
                $q->where('type', $this->type);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.seller.wallet.seller-wallet-transaction-list', [
            'transactions' => $transactions,
            'types' => TransactionType::cases(),
        ]);
    }
}
