<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\CartType;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Slider;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function home()
    {
        $sliders = Cache::remember('sliders',60*60*24*10,function (){
            return Slider::query()->get();
        });
        $brands = Cache::remember('brands',60*60*24*10,function (){
            return Brand::query()->get();
        });
        $most_sold = Product::query()->with('category')->orderBy('sold','DESC')->get();
        $newest_products = Product::query()->with('category')->orderBy('created_at','DESC')->get();
        $spacial_products = ProductPrice::query()->with('product')
            ->where('spacial_start','<=',now())
            ->where('spacial_expiration','>=',now())
            ->where('count','>',0)
            ->get();

        // پیشنهاد لحظه ای

         $instant_offers = Product::smartOffer()
        ->limit(9)
        ->get();

        return view('frontend.index',compact('sliders'
            ,'most_sold','spacial_products','brands','newest_products','instant_offers'));
    }

    public function userCart()
    {
        $carts_count = UserCart::query()
            ->where('user_id',auth()->id())
            ->where('type',CartType::Main->value)->count();
        return view('frontend.user_cart',compact('carts_count'));
    }
    public function shopping()
    {
        return view('frontend.shopping');
    }

    public function shoppingPayment()
    {
        return view('frontend.shopping_payment');
    }
}
