<?php

namespace App\Livewire\Admin\GiftCarts;

use App\Helpers\CreateUniqueCode;
use App\Helpers\DateManager;
use App\Models\Discount;
use App\Models\GiftCart;
use App\Models\User;
use Livewire\Component;

class AddGiftCart extends Component
{
    public $users;
    public $selected_user;
    public $search;
    public $gift_price;
    public $gift_title;
    public $expiration_date;

    public function mount()
    {
        $this->users = collect();
        $this->selected_user = null;
    }

    public function submit()
    {
        $this->users = User::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('mobile', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->get();
    }

    public function addGiftCart()
    {
        $this->validate([
            'selected_user' => 'required',
            'gift_title' => 'required|string|max:255',
            'gift_price' => 'required|numeric|min:1000', // قیمت عددی و حداقل هزار تومان
            'expiration_date' => 'required',
        ], [
            'selected_user.required' => 'لطفاً ابتدا یک کاربر را جستجو و انتخاب کنید.',
            'gift_price.numeric' => 'مبلغ باید به صورت عدد باشد.',
        ]);

        GiftCart::query()->create([
            'user_id' => $this->selected_user['id'],
            'code' => CreateUniqueCode::generateRandomString(6, GiftCart::class),
            'gift_title' => $this->gift_title,
            'gift_price' => $this->gift_price,
            'balance' => $this->gift_price, // <--- حل مشکل SQLSTATE 1364
            'expiration_date' => DateManager::shamsi_to_miladi($this->expiration_date)
        ]);

        $this->reset([
            'gift_title',
            'gift_price',
            'expiration_date',
            'users',
            'search',
            'selected_user'
        ]);

        $this->users = collect();
        session()->flash('message', 'کارت هدیه با موفقیت برای کاربر صادر شد.');
    }

    public function selectUser($user)
    {
        $this->selected_user = $user;
    }

    public function render()
    {
        return view('livewire.admin.gift-carts.add-gift-cart');
    }
}
