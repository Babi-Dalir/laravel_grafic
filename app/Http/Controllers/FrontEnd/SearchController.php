<?php

namespace App\Http\Controllers\FrontEnd;

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
                'categories' => [],
            ]);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->take(10)
            ->get();

        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->take(5)
            ->get();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
