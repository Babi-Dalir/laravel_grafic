<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\CartType;
use App\Enums\DiscountCampaignStatus;
use App\Enums\DiscountCampaignType;
use App\Enums\SliderStatus;
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
        $now = now();

        $sliders = Cache::remember('sliders',60*60*24*10,function (){
            return Slider::query()
                ->where('status',SliderStatus::Active->value)->get();
        });
        $most_sold = Product::query()
            ->with(['category','campaignTargets.campaign'])
            ->orderBy('sold','DESC')
            ->get();
        $newest_products = Product::query()
            ->with(['category', 'campaignTargets.campaign'])
            ->orderBy('created_at','DESC')
            ->get();
        $spacial_products = Product::query()
            ->with(['category', 'campaignTargets.campaign'])
            ->whereHas('campaignTargets.campaign', function ($query) use ($now) {
                $query->where('status',DiscountCampaignStatus::Active->value)
                    ->where('starts_at', '<=', $now)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                    });
            })
            ->take(10)
            ->get();
        // پیشنهاد لحظه ای

         $instant_offers = Product::smartOffer()
        ->limit(9)
        ->get();

        return view('frontend.index',compact('sliders'
            ,'most_sold','spacial_products','newest_products','instant_offers'));
    }

    public function userCart()
    {
        $carts_count = UserCart::query()
            ->where('user_id',auth()->id())
            ->where('type',CartType::Main->value)->count();
        return view('frontend.user_cart',compact('carts_count'));
    }
}
