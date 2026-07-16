<?php

namespace App\Livewire\Admin\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

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

        // 🟢 پچ طلایی: مدیریت اتمیک اکشن پرداخت دستی ادمین
        if ($nextStatus === OrderStatus::Payed) {
            try {
                // صدا زدن مستقیم متد جامع موفقیت آمیز فاکتور که تمام لجرها و سهم‌ها را اتمیک ثبت می‌کند
                Order::successPayment($order, 'MANUAL_ADMIN_CONFIRM_' . auth()->id());

                $this->dispatch('order-status-updated', message: 'سفارش با موفقیت به صورت دستی تایید و اسناد مالی صادر شدند.');
            } catch (\Exception $e) {
                $this->dispatch('order-status-error', message: 'خطا در ثبت اسناد مالی: ' . $e->getMessage());
            }
            return;
        }

        // 🔵 مدیریت سایر اکشن‌ها (لغو، تلاش مجدد و...) خارج از جریان واریز نهایی
        DB::transaction(function () use ($order, $nextStatus) {
            $order->update([
                'status' => $nextStatus
            ]);
        });

        $this->dispatch('order-status-updated', message: 'وضعیت سفارش با موفقیت به روز رسانی شد.');
    }

    public function render()
    {
        // 🚀 بهینه‌سازی نهایی دیتابیس با لود همزمان تمام ریلیشن‌های مورد نیاز در لایه نمایش
        $orders = Order::query()
            ->with(['user', 'orderDetails'])
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
            ->paginate(15);

        return view('livewire.admin.order.orders', compact('orders'));
    }
}
