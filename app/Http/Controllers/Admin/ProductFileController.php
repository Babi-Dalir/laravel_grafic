<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductFileController extends Controller
{
    public function index(Product $product)
    {
        $files = $product
            ->files()
            ->latest()
            ->get();

        return view('admin.products.files_list',compact('product', 'files'));
    }
}
