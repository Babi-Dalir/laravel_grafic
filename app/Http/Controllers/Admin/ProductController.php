<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $title = "لیست محصولات";
        return view('admin.products.list', compact('title'));
    }

    public function create()
    {
        $title = "ایجاد محصول";
        $categories = Category::getCategories();
        $tags = Tag::query()->pluck('name', 'id');
        return view('admin.products.create', compact('title', 'categories', 'tags'));
    }

    public function store(ProductRequest $request)
    {
        $product = Product::createProduct($request);

        return redirect()
            ->route('add.product.gallery', $product->id)
            ->with('message', 'محصول با موفقیت ایجاد شد. اکنون تصاویر گالری آن را آپلود کنید.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        $title = "ویرایش محصول";
        $categories = Category::getCategories();
        $tags = Tag::query()->pluck('name', 'id');
        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('title', 'categories', 'tags', 'product'));
    }

    public function update(ProductRequest $request, string $id)
    {
        Product::updateProduct($request, $id);

        return redirect()
            ->route('products.index')
            ->with('message', 'محصول با موفقیت ویرایش و بروزرسانی شد.');
    }

    public function destroy(string $id)
    {

    }

    public function trashed()
    {
        $title = "لیست محصولات حذف شده";
        return view('admin.products.trashed_list', compact('title'));
    }

    public function addGallery($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.add_gallery', compact('product'));
    }

    public function storeGallery(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048'
        ]);

        Gallery::query()->create([
            'product_id' => $id,
            'image' => ImageManager::saveProductImage('products', $request->file('file')),
            'position' => Gallery::query()->where('product_id', $id)->count()
        ]);

        return redirect()->back()->with('message', 'تصویر به گالری اضافه شد.');
    }

    public function createProductProperty(Product $product)
    {
        return view('admin.products.product_properties', compact('product'));
    }
}
