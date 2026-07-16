<?php

namespace App\Livewire\Admin\Order;

use App\Enums\OrderDetailStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use Livewire\Component;
use Livewire\WithPagination;

class OrderDetails extends Component
{
    use WithPagination;

    public Order $order;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    /**
     * ماتریس مجاز تغییر وضعیت جزئیات فاکتور (State Machine)
     */
    protected const STATE_MACHINE = [
        OrderDetailStatus::Waiting->value    => ['pay' => OrderDetailStatus::Paid, 'refund' => OrderDetailStatus::Refunded],
        OrderDetailStatus::Paid->value       => ['download' => OrderDetailStatus::Downloaded, 'refund' => OrderDetailStatus::Refunded],
        OrderDetailStatus::Downloaded->value => ['refund' => OrderDetailStatus::Refunded],
        OrderDetailStatus::Refunded->value   => ['reset' => OrderDetailStatus::Waiting],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * تغییر وضعیت اکشن‌محور بدون ریسک رفتارهای ناخواسته مالی
     */
    public function changeOrderDetailStatus($id, $action)
    {
        abort_unless(auth()->user()->hasRole('مدیر'), 403, 'شما دسترسی به این بخش را ندارید.');

        $orderDetail = OrderDetail::findOrFail($id);
        $nextStatus = self::STATE_MACHINE[$orderDetail->status->value][$action] ?? null;

        if (!$nextStatus) {
            $this->dispatch('order-detail-error', message: 'تغییر وضعیت درخواستی مجاز نیست.');
            return;
        }

        // 🟢 اصلاح کستینگ انوم به صورت کاملاً تمیز و سازگار با مدل لاراول
        $orderDetail->update([
            'status' => $nextStatus->value
        ]);

        $this->dispatch('order-detail-updated', message: 'وضعیت آیتم با موفقیت تغییر کرد.');
    }

    public function render()
    {
        $order_details = OrderDetail::query()
            ->with([
                'download',
                // 🟢 لود اتمیک رابطه seller به همراه رابطه زنده کاربر جهت جلوگیری از باگ N+1 در پنل ادمین
                'product' => function($q) {
                    $q->withTrashed()->with([
                        'seller',
                        'user',
                        'category.commission'
                    ]);
                }
            ])
            ->where('order_id', $this->order->id)
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.order.order-details', compact('order_details'));
    }
}
