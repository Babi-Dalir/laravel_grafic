<?php

namespace App\Livewire\Admin\Transactions;

use App\Models\SellerWalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class SellerTransactionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    /**
     * 🟢 حل باگ پجینیشن در زمان سرچ همزمان
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function searchData()
    {
        $this->resetPage();
    }

    public function render()
    {
        $transactions = SellerWalletTransaction::query()
            // لود بهینه روابط جهت جلوگیری از کوئری‌های مکرر
            ->with(['seller:id,user_id,first_name,last_name,brand_name,national_code', 'order:id,order_code'])
            ->when(filled($this->search), function ($q) {
                $q->where(function ($query) {
                    $searchTerm = "%{$this->search}%";

                    $query->whereHas('seller', function ($sellerQuery) use ($searchTerm) {
                        $sellerQuery->where('brand_name', 'like', $searchTerm)
                            ->orWhere('first_name', 'like', $searchTerm)
                            ->orWhere('last_name', 'like', $searchTerm)
                            ->orWhere('national_code', 'like', $searchTerm);
                    })
                        ->orWhereHas('order', function ($orderQuery) use ($searchTerm) {
                            $orderQuery->where('order_code', 'like', $searchTerm);
                        });
                });
            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.transactions.seller-transaction-list', compact('transactions'));
    }
}
