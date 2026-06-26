<?php

namespace App\Livewire\Frontend\Carts;

use App\Enums\CartType;
use App\Enums\ProductStatus;
use App\Models\UserCart;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class HeaderCarts extends Component
{
    /**
     * حذف محصول از سبد خرید کاربر با رعایت حریم امنیتی رکورد
     */
    public function deleteCart($cart_id)
    {
        $user_cart = UserCart::query()
            ->where('id', $cart_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$user_cart) {
            return;
        }

        $user_cart->delete();

        // 🟢 دیسپچ رویداد برای رفرش همین کامپوننت و کامپوننت صفحه اصلی سبد خرید
        $this->dispatch('deleteProductCart');
    }

    /**
     * 🟢 حل باگ رفرش: این متد با دیسپچ بالا صدا زده می‌شود و استیت لایووایر را مجبور به رندر آنی می‌کند
     */
    #[On('deleteProductCart')]
    public function refreshCarts()
    {
        // لایووایر ۳ به صورت خودکار با اجرای یک متد لیسنر، مقادیر Computed را بازخوانی و رندر می‌کند.
    }

    /**
     * 🟢 تعریف ویژگی محاسباتی کش‌شونده (Computed Property) برای افزایش سرعت لود هدر
     */
    #[Computed]
    public function carts()
    {
        if (!auth()->check()) {
            return collect();
        }

        return UserCart::query()
            ->with(['product' => function ($q) {
                $q->where('status', ProductStatus::Approved->value);
            }])
            ->where('user_id', auth()->id())
            ->where('type', CartType::Main->value)
            ->get()
            ->filter(fn($cart) => $cart->product);
    }

    public function render()
    {
        // محاسبات بر پایه ویژگی‌های کش‌شده کپسوله‌سازی می‌شود
        $carts = $this->carts;

        $totalPrice = $carts->sum(fn($cart) => $cart->product->final_price);

        $discountPrice = $carts->sum(function($cart) {
            return max(0, $cart->product->main_price - $cart->product->final_price);
        });

        $finalPrice = max($totalPrice, 0);

        return view('livewire.frontend.carts.header-carts', [
            'carts'         => $carts,
            'total_price'   => $totalPrice,
            'discount_price'=> $discountPrice,
            'final_price'   => $finalPrice,
        ]);
    }
}
