<?php

namespace App\Livewire\Frontend\Carts;

use App\Enums\CartType;
use App\Enums\ProductStatus;
use App\Models\Discount;
use App\Models\GiftCart;
use App\Models\UserCart;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class CartsDetail extends Component
{
    public $discount_code = '';
    public $gift_cart_code = '';

    public $discount_code_price = 0;
    public $gift_cart_price = 0;

    /**
     * پاکسازی محصولات منقضی یا حذف شده (فقط یک‌بار در لود اولیه صفحه)
     */
    public function mount()
    {
        if (auth()->check()) {
            UserCart::query()
                ->where('user_id', auth()->id())
                ->where(function ($q) {
                    $q->whereHas('product', function ($p) {
                        $p->whereIn('status', [
                            ProductStatus::Archived->value,
                            ProductStatus::Rejected->value,
                            ProductStatus::Draft->value
                        ]);
                    })->orWhereDoesntHave('product');
                })
                ->delete();
        }
    }

    public function submitPayment()
    {
        Session::put('shop_data', [
            'discount_code'  => $this->discount_code,
            'gift_cart_code' => $this->gift_cart_code,
            'payment_type'   => 'zarinpal',
        ]);

        return redirect()->route('payment');
    }

    public function discountCode()
    {
        $discount = Discount::query()
            ->where('code', trim($this->discount_code))
            ->where('discount', '>', 0)
            ->where('expiration_date', '>=', now())
            ->first();

        if ($discount) {
            $this->discount_code_price = $discount->discount;
            session()->flash('success_discount', 'کد تخفیف با موفقیت اعمال شد.');
        } else {
            $this->discount_code_price = 0;
            session()->flash('warning_discount', 'کد تخفیف نامعتبر یا منقضی شده است.');
        }
    }

    public function giftCartCode()
    {
        $gift_cart = GiftCart::query()
            ->where('code', trim($this->gift_cart_code))
            ->where('user_id', auth()->id())
            ->where('balance', '>', 0)
            ->where('expiration_date', '>=', now())
            ->first();

        if ($gift_cart) {
            $this->gift_cart_price = $gift_cart->balance;
            session()->flash('success_gift_cart', 'کارت هدیه با موفقیت اعمال شد.');
        } else {
            $this->gift_cart_price = 0;
            session()->flash('warning_gift_cart', 'کارت هدیه نامعتبر یا منقضی شده است.');
        }
    }

    public function moveToReserveCart($cart_id)
    {
        UserCart::query()
            ->where('id', $cart_id)
            ->where('user_id', auth()->id())
            ->update(['type' => CartType::Reserve->value]);
    }

    public function moveToMainCart($cart_id)
    {
        UserCart::query()
            ->where('id', $cart_id)
            ->where('user_id', auth()->id())
            ->update(['type' => CartType::Main->value]);
    }

    public function moveToAllMainCart()
    {
        UserCart::query()
            ->where('user_id', auth()->id())
            ->where('type', CartType::Reserve->value)
            ->update(['type' => CartType::Main->value]);
    }

    public function deleteCart($cart_id)
    {
        UserCart::query()
            ->where('id', $cart_id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function render()
    {
        // دریافت خام سبد خرید بدون فیلترهای مخرب در کوئری
        $cartsQuery = UserCart::query()
            ->with('product')
            ->where('user_id', auth()->id())
            ->where('type', CartType::Main->value)
            ->get();

        $reserveCartsQuery = UserCart::query()
            ->with('product')
            ->where('user_id', auth()->id())
            ->where('type', CartType::Reserve->value)
            ->get();

        // فیلتر امن محصولات تایید شده (دقیقا مطابق منطق اولیه خودت)
        $carts = $cartsQuery->filter(function ($cart) {
            return $cart->product && $cart->product->status === ProductStatus::Approved->value;
        });

        $reserve_carts = $reserveCartsQuery->filter(function ($cart) {
            return $cart->product && $cart->product->status === ProductStatus::Approved->value;
        });

        // محاسبات مالی فاکتور
        $total_price = 0;
        $discount_price = 0;

        foreach ($carts as $cart) {
            $total_price += $cart->product->final_price;
            $discount_price += ($cart->product->main_price - $cart->product->final_price);
        }

        // جلوگیری از منفی شدن فاکتور
        $allowed_discount_code = min($this->discount_code_price, $total_price);
        $allowed_gift_cart = min($this->gift_cart_price, ($total_price - $allowed_discount_code));

        $final_price = max(($total_price - $allowed_discount_code - $allowed_gift_cart), 0);

        return view('livewire.frontend.carts.carts-detail', [
            'carts'          => $carts,
            'reserve_carts'  => $reserve_carts,
            'total_price'    => $total_price,
            'discount_price' => $discount_price,
            'final_price'    => $final_price,
        ]);
    }
}
