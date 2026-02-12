<?php

namespace App\Models;


use App\Enums\CommentStatus;
use App\Enums\ProductStatus;
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
        'price',
        'discount',
        'count',
        'max_sell',
        'viewed',
        'sold',
        'image',
        'guaranty_id',
        'description',
        'spacial_start',
        'spacial_expiration',
        'status',
        'category_id',
        'brand_id',
        'user_id',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'color_product');
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

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function guaranty()
    {
        return $this->belongsTo(Guaranty::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function productStars()
    {
        return $this->hasMany(ProductStar::class);
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
        $product = Product::query()->create([
            'user_id' => auth()->user()->id,
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->e_name),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'brand_id' => $request->input('brand_id'),
            'image' => ImageManager::saveImage('products', $request->image),
        ]);
        $product->tags()->attach($request->tags);
    }

    public static function updateProduct($request, $id)
    {
        $product = Product::query()->find($id);
        $product->update([
            'user_id' => auth()->user()->id,
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->e_name),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'brand_id' => $request->input('brand_id'),
            'image' => $request->image ? ImageManager::saveImage('products', $request->image) : $product->image,
        ]);
        $product->tags()->sync($request->tags);
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
            ->where('count', '>', 0)
            ->orderByDesc('discount') // تخفیف بیشتر
            ->orderBy('count');       // موجودی کمتر
    }
}
