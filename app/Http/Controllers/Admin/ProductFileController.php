<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileManager;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FileValidation\ZipScannerService;
use Illuminate\Support\Facades\DB;

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
