<?php

namespace App\Livewire\Admin\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    /**
     * پیاده‌سازی یک State Machine واقعی و تعریف دقیق اکشن‌های مجاز بر اساس وضعیت فعلی
     */
    protected const STATE_MACHINE = [
        OrderStatus::WaitPayment->value => [
            'pay'    => OrderStatus::Payed,
            'cancel' => OrderStatus::Cancelled,
        ],
        OrderStatus::Payed->value => [
            'fail' => OrderStatus::Failed, // فرآیند عودت وجه یا نقض تراکنش مالی
        ],
        OrderStatus::Failed->value => [
            'retry' => OrderStatus::WaitPayment,
        ],
        OrderStatus::Cancelled->value => [
            'renew' => OrderStatus::WaitPayment,
        ],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * تغییر وضعیت با ارسال نوع اکشن مشخص (Action-driven State Transition)
     */
    public function changeOrderStatus($id, $action)
    {
        abort_unless(auth()->user()->hasRole('مدیر'), 403, 'شما دسترسی به این عملیات را ندارید.');

        $order = Order::findOrFail($id);
        $currentStatus = $order->status;

        // بررسی اینکه آیا اکشن کلیک شده، روی وضعیت فعلی معتبر است یا خیر
        $nextStatus = self::STATE_MACHINE[$currentStatus->value][$action] ?? null;

        if (!$nextStatus) {
            $this->dispatch('order-status-error', message: 'تغییر وضعیت درخواستی از نظر مالی غیرمجاز است.');
            return;
        }

        $data = ['status' => $nextStatus];

        if ($nextStatus === OrderStatus::Payed) {
            $data['paid_at'] = now();
        }

        $order->update($data);

        // 🟢 بهینه‌سازی سرور: اگر وضعیت به پرداخت شده تغییر کرد، متد پردازش تراکنش موفق خودت را صدا می‌زنیم
        if ($nextStatus === OrderStatus::Payed) {
            Order::successPayment($order);
        }

        $this->dispatch('order-status-updated', message: 'وضعیت سفارش با موفقیت به روز رسانی شد.');
    }

    public function render()
    {
        // 🟢 بهینه‌سازی آنلاین: جلوگیری از قفل شدن دیتابیس با لود بهینه ریلیشن‌ها
        $orders = Order::query()
            ->with(['user', 'orderDetails', 'sellerWalletTransactions'])
            ->when($this->search, function ($query) {
                $query->where(function ($mainQuery) {
                    $mainQuery->where('order_code', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', "%{$this->search}%")
                                ->orWhere('mobile', 'like', "%{$this->search}%")
                                ->orWhere('email', 'like', "%{$this->search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.order.orders', compact('orders'));
    }
}
