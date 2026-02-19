<?php

namespace App\Models;


use App\Enums\CommentStatus;
use App\Enums\ProductStatus;
use App\Helpers\DateManager;
use App\Helpers\FileManager;
use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($request) {
            $slug = str()->slug($request->e_name, '-', null);
            $mainFile = $request->file('main_file');

            // محاسبه قیمت نهایی
            $mainPrice = $request->input('main_price', 0);
            $discount = $request->input('discount', 0);
            $price = $mainPrice - ($mainPrice * $discount / 100);

            $product = Product::query()->create([
                'user_id' => auth()->user()->id,
                'category_id' => $request->input('category_id'),
                'name' => $request->input('name'),
                'e_name' => $request->input('e_name'),
                'slug' => $slug,
                'description' => $request->input('description'),
                'main_price' => $mainPrice,
                'discount' => $discount,
                'price' => $price,
                'image' => $request->image ? ImageManager::saveImage('products', $request->image) : null,
                // مدیریت فایل دیجیتال
                'main_file' => FileManager::saveDigitalFile($mainFile, $slug),
                'file_size' => $mainFile ? $mainFile->getSize() : 0, // ذخیره حجم فایل به بایت
                // تاریخ‌های تخفیف ویژه
                'spacial_start' => $request->filled('spacial_start') ? DateManager::shamsi_to_miladi($request->spacial_start) : null,
                'spacial_expiration' => $request->filled('spacial_expiration') ? DateManager::shamsi_to_miladi($request->spacial_expiration) : null,
            ]);

            if ($request->filled('tags')) {
                $product->tags()->attach($request->tags);
            }

            return $product;
        });
    }

    public static function updateProduct($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $product = Product::query()->findOrFail($id);
            $slug = str($request->e_name)->slug('-', null);

            $mainPrice = $request->input('main_price', 0);
            $discount = $request->input('discount', 0);
            $price = $mainPrice - ($mainPrice * $discount / 100);

            // ۱. مدیریت تصویر (اگر تصویر جدید آپلود شد، قبلی حذف شود)
            if ($request->hasFile('image')) {
                ImageManager::unlinkImage('products', $product); // حذف عکس قبلی
                $imageName = ImageManager::saveImage('products', $request->image);
            }

            // ۲. مدیریت فایل دیجیتال
            if ($request->hasFile('main_file')) {
                // حذف فایل قدیمی از دیسک
                FileManager::deleteDigitalFile($product->slug, $product->main_file);

                // ذخیره فایل جدید و آپدیت حجم
                $newFile = $request->file('main_file');
                $product->main_file = FileManager::saveDigitalFile($newFile, $slug);
                $product->file_size = $newFile->getSize();
            }

            $product->update([
                'name' => $request->input('name'),
                'e_name' => $request->input('e_name'),
                'slug' => $slug,
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'main_price' => $mainPrice,
                'discount' => $discount,
                'price' => $price,
                'image' => $request->image ? $imageName : $product->image,
                'spacial_start' => $request->filled('spacial_start') ? DateManager::shamsi_to_miladi($request->spacial_start) : null,
                'spacial_expiration' => $request->filled('spacial_expiration') ? DateManager::shamsi_to_miladi($request->spacial_expiration) : null,
            ]);

            if ($request->filled('tags')) {
                $product->tags()->sync($request->tags);
            }

            return $product;
        });
    }
    protected static function boot()
    {
        parent::boot();

        // استفاده از deleting به جای forceDeleted برای کنترل دقیق‌تر
        static::deleting(function ($product) {
            if ($product->isForceDeleting()) {
                ImageManager::unlinkImage('products', $product);

                if ($product->main_file) {
                    FileManager::deleteDigitalFile($product->slug, $product->main_file);
                }
            }
        });
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
