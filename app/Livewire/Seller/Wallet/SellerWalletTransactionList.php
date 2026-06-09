<?php

namespace App\Livewire\Seller\Wallet;

use App\Enums\TransactionType;
use App\Models\SellerWalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class SellerWalletTransactionList extends Component
{
    use WithPagination;

    public $search = '';
    public $type = null;

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $seller = auth()->user()->seller;

        $transactions = SellerWalletTransaction::query()
            ->with(['order'])
            ->where('seller_id', $seller->id)

            ->when($this->search, fn($q) =>
            $q->where('description', 'like', "%{$this->search}%")
            )

            ->when($this->type, fn($q) =>
            $q->where('type', $this->type)
            )

            ->latest()
            ->paginate(10);

        return view('livewire.seller.wallet.seller-wallet-transaction-list', [
            'transactions' => $transactions,
            'seller' => $seller,

            // 🧠 derived from ledger
            'pending' => $seller->pending_balance,
            'settled' => $seller->settled_balance,
            'balance' => $seller->balance,

            'canWithdraw' => $seller->available_balance >= 100000,
            'types' => TransactionType::cases(),
        ]);
    }
}
