<?php

namespace App\Livewire\Frontend\Products;

use App\Models\Favorite;
use App\Models\ProductPrice;
use App\Models\UserCart;
use Livewire\Component;

class SingleProduct extends Component
{
    public $product;
    public $product_price;

    public function mount()
    {
        $this->product_price = ProductPrice::query()->where('product_id',$this->product->id)->orderBy('price','ASC')->first();
    }

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

    public function addToCart($color_id,$guaranty_id)
    {
        if (auth()->user()){
            $user_cart = UserCart::query()
                ->where('user_id',auth()->user()->id)
                ->where('product_id',$this->product->id)
                ->where('color_id',$color_id)
                ->where('guaranty_id',$guaranty_id)
                ->first();
            if ($user_cart){
                $user_cart->update([
                    'count'=>$user_cart->count +1
                ]);
            }else{
                UserCart::query()->create([
                    'user_id'=>auth()->user()->id,
                    'product_id'=>$this->product->id,
                    'color_id'=>$color_id,
                    'guaranty_id'=>$guaranty_id,
                    'count'=>1,
                ]);
            }

            return redirect()->route('user.cart');
        }else{
            return redirect()->route('login');
        }
    }
    public function changeColorProduct($color_id)
    {
        $this->product_price = ProductPrice::query()
            ->where('product_id',$this->product->id)
            ->where('color_id',$color_id)
            ->orderBy('price','ASC')
            ->first();
    }
    public function render()
    {
        return view('livewire.frontend.products.single-product');
    }
}
