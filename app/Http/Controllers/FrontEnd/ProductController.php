<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function singleProduct($slug)
    {
        $product = Product::query()
            ->with(['category','brand','colors','tags','properties','propertyGroups','productPrices'])
            ->where('slug',$slug)->first();
        $product->increment('viewed');
        return view('frontend.single_product',compact('product'));
    }

    public function mainCategoryProductList($main_slug)
    {
        $sub_slug=null;
        $child_slug=null;
        return view('frontend.category_product_list', compact('main_slug','sub_slug','child_slug'));
    }

    public function searchCategoryProductList($sub_slug,$child_slug=null)
    {
        $main_slug=null;
        return view('frontend.category_product_list', compact('main_slug','sub_slug','child_slug'));
    }

    public function compareProducts($product_id_1,$product_id_2)
    {
        return view('frontend.compare_products', compact('product_id_1', 'product_id_2'));
    }

    public function productComment($product_id)
    {
        $product = Product::query()->find($product_id);
        return view('frontend.product_comment', compact('product'));
    }
}
