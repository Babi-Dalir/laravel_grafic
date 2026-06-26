<?php

namespace App\Livewire\Frontend\Products;

use App\Enums\CartType;
use App\Models\Favorite;
use App\Models\UserCart;
use Livewire\Component;

class SingleProduct extends Component
{
    public $product;

    /**
     * مدیریت افزودن یا حذف از لیست علاقه‌مندی‌ها
     */
    public function AddFavorite($product_id)
    {
        if (!auth()->check()) {
            session()->flash('message', 'برای افزودن به لیست علاقه‌مندی‌ها حتما باید در سایت ثبت‌نام کنید یا وارد شوید.');
            return;
        }

        $userId = auth()->id();

        $favorite = Favorite::query()
            ->where('user_id', $userId)
            ->where('product_id', $product_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            Favorite::query()->create([
                'user_id'    => $userId,
                'product_id' => $product_id
            ]);
        }
    }

    /**
     * افزودن محصول به سبد خرید اصلی
     */
    public function addToCart()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();

        $userCart = UserCart::query()
            ->where('user_id', $userId)
            ->where('product_id', $this->product->id)
            ->first();

        if (!$userCart) {
            UserCart::create([
                'user_id'    => $userId,
                'product_id' => $this->product->id,
                'type'       => CartType::Main->value,
            ]);

            // 🟢 دیسپچ رویداد جهت همگام‌سازی آنی شمارنده سبد خرید در هدر سایت
            $this->dispatch('deleteProductCart');
        }

        return redirect()->route('user.cart');
    }

    public function render()
    {
        // 🟢 انتقال منطق بررسی علاقه‌مندی از بلید به کامپوننت جهت جلوگیری از مشکل N+1
        $isFavorite = false;

        if (auth()->check()) {
            $isFavorite = Favorite::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $this->product->id)
                ->exists();
        }

        // لود کردن روابط ویژگی‌ها به صورت حریصانه (Eager Loading) برای سرعت بالاتر
        $this->product->loadMissing(['propertyGroups.properties', 'galleries']);

        return view('livewire.frontend.products.single-product', [
            'isFavorite' => $isFavorite
        ]);
    }
}
