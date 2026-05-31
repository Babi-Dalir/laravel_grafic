<?php

namespace App\Livewire\Frontend\Products;

use App\Enums\CartType;
use App\Models\Favorite;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Livewire\Component;

class SingleProduct extends Component
{
    public $product;

    public function AddFavorite($product_id)
    {
        if (auth()->user()){
            $favorite = Favorite::query()
                ->where('user_id',auth()->user()->id)
                ->where('product_id',$product_id)
                ->first();
            if ($favorite){
                $favorite->delete();
            }else{
                Favorite::query()->create([
                    'user_id'=>auth()->user()->id,
                    'product_id'=>$product_id
                ]);
            }
        }else{
            session()->flash('message','برای افزودن به لیست علاقمندی ها حتما باید در سایت ثبت نام یا ورود کنید');
        }
    }

    public function addToCart()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userCart = UserCart::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $this->product->id)
            ->first();

        if (!$userCart) {
            UserCart::create([
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
                'type' => CartType::Main->value,
            ]);
        }

        return redirect()->route('user.cart');
    }
    public function render()
    {
        return view('livewire.frontend.products.single-product');
    }
}
