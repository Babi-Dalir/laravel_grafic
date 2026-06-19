<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Support\Facades\Storage;

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

    public function downloadFile(ProductFile $file)
    {
        $path = $file->path;

        if (! Storage::disk('digital_files')->exists($path)) {
            abort(404, 'فایل یافت نشد');
        }

        return Storage::disk('digital_files')->download(
            $path,
            $file->original_name
        );
    }
}
