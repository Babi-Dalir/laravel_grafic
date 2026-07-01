<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class SearchController extends Controller
{
    public function ajaxSearch(Request $request)
    {
        $query = $request->get('query', '');

        if (mb_strlen($query) < 2) {
            return response()->json([
                'products' => [],
            ]);
        }

        $products = Product::select(['id', 'name', 'slug'])
            ->where('status', ProductStatus::Approved->value)
            ->where('name', 'LIKE', "%{$query}%")
            ->take(8) // نمایش ۸ محصول اول برای شیک ماندن باکس سرچ
            ->get();


        return response()->json([
            'products' => $products,
        ]);
    }
}
