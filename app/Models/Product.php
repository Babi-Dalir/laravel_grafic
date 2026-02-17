<?php

namespace App\Models;


use App\Enums\CommentStatus;
use App\Enums\ProductStatus;
use App\Helpers\DateManager;
use App\Helpers\FileManager;
use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'e_name',
        'slug',
        'main_price',
        'price',
        'discount',
        'sold',
        'image',
        'description',
        'main_file',
        'file_size',
        'download_count',
        'spacial_start',
        'spacial_expiration',
        'status',
        'user_id',
        'category_id',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function propertyGroups()
    {
        return $this->belongsToMany(PropertyGroup::class, 'product_property_group');
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('status', CommentStatus::Approved->value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createProduct($request)
    {
        $slug = str()->slug($request->e_name);

        $price = $request->input('main_price', 0) - ($request->input('main_price', 0) * $request->input('discount', 0) / 100);

        $product = Product::query()->create([
            'user_id' => auth()->user()->id,
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'main_file' => FileManager::saveDigitalFile($request->file('main_file'), $slug),
            'image' => $request->image ? ImageManager::saveImage('products', $request->image) : null,
            'main_price' => $request->input('main_price'),
            'discount' => $request->input('discount'),
            'price' => $price,
            'spacial_start' => $request->input('spacial_start') != null ? DateManager::shamsi_to_miladi($request->input('spacial_start')) : null,
            'spacial_expiration' => $request->input('spacial_expiration') != null ? DateManager::shamsi_to_miladi($request->input('spacial_expiration')) : null,
        ]);

        // اضافه کردن تگ‌ها اگر ارسال شده
//        if ($request->filled('tags')) {
//            $product->tags()->attach($request->tags);
//        }

        return $product;
    }


    public static function updateProduct($request, $id)
    {
        $product = Product::query()->findOrFail($id);

        $slug = str()->slug($request->e_name);

        $price = $request->input('main_price', 0) - ($request->input('main_price', 0) * $request->input('discount', 0) / 100);

        // اگر فایل جدید آپلود شد، قبلی حذف شود
        if ($request->hasFile('main_file') && $product->main_file) {
            FileManager::deleteDigitalFile($product->slug, $product->main_file);
        }

        $product->update([
            'user_id' => auth()->user()->id,
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'main_file' => $request->hasFile('main_file') ? FileManager::saveDigitalFile($request->file('main_file'), $slug) : $product->main_file,
            'image' => $request->image ? ImageManager::saveImage('products', $request->image) : $product->image,
            'main_price' => $request->input('main_price'),
            'discount' => $request->input('discount'),
            'price' => $price,
            'spacial_start' => $request->input('spacial_start') != null ? DateManager::shamsi_to_miladi($request->input('spacial_start')) : null,
            'spacial_expiration' => $request->input('spacial_expiration') != null ? DateManager::shamsi_to_miladi($request->input('spacial_expiration')) : null,
        ]);

        // سینک تگ‌ها
//        if ($request->filled('tags')) {
//            $product->tags()->sync($request->tags);
//        }

        return $product;
    }


    protected $appends = ['final_price'];

    public function getFinalPriceAttribute()
    {
        return $this->price - ($this->price * $this->discount / 100);
    }

    // الگوریتم پیشنهاد لحظه‌ای
    public function scopeSmartOffer($query)
    {
        return $query
            ->where('status', ProductStatus::Active->value)
            ->where('discount', '>', 0)
            ->orderByDesc('discount'); // تخفیف بیشتر

    }
}
