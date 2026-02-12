<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'e_name',
        'slug',
        'image',
        'parent_id',

    ];

    public function parentCategory()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id')
            ->withTrashed()
            ->withDefault(['name' => 'دسته پدر']);
    }

    public function childCategory()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    public static function createCategory($request)
    {
        Category::query()->create([
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->e_name),
            'image' => ImageManager::saveImage('categories', $request->image),
            'parent_id' => $request->input('parent_id'),

        ]);
    }

    public static function updateCategory($request, $category)
    {
        $category->update([
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->e_name),
            'image' => $request->image ? ImageManager::saveImage('categories', $request->image) : $category->image,
            'parent_id' => $request->input('parent_id'),

        ]);
    }

    public static function getCategories()
    {
        $array = [];
        $categories = self::query()->with('childCategory')->where('parent_id', 0)->get();
        foreach ($categories as $category1) {
            $array[$category1->id] = $category1->name;
            foreach ($category1->childCategory as $category2) {
                $array[$category2->id] = ' - ' . $category2->name;
                foreach ($category2->childCategory as $category3) {
                    $array[$category3->id] = ' - - ' . $category3->name;
                }
            }
        }
        return $array;
    }
    public static function getLayer3Categories()
    {
        $array = [];
        $categories = self::query()->with('childCategory')->where('parent_id', 0)->get();
        foreach ($categories as $category1) {
            foreach ($category1->childCategory as $category2) {
                foreach ($category2->childCategory as $category3) {
                    $array[$category3->id] = $category3->name;
                }
            }
        }
        return $array;
    }

    public static function getProductCategoryCount($id)
    {
        $sum = 0;
        $categories = self::query()->with('childCategory')->where('parent_id', $id)->get();
        foreach ($categories as $category1) {
            foreach ($category1->childCategory as $category2) {
                $sum += $category2->products()->count();
            }
        }
        return $sum;
    }

    protected static function boot()
    {
        parent::boot();
        self::deleting(function ($category) {
            foreach ($category->childCategory()->withTrashed()->get() as $cat) {
                if ($category->isForceDeleting()) {
                    $cat->forcedelete();
                } else {
                    $cat->delete();
                }
            }
        });
        self::restoring(function ($category) {
            foreach ($category->childCategory()->withTrashed()->get() as $cat) {
                $cat->restore();
            }
        });
    }

    public static function getProductByCategory($main_slug, $sub_slug, $child_slug, $column, $orderBy, $page)
    {
        if ($main_slug != null && $sub_slug == null && $child_slug == null) {
            return Category::getProductListByMainCategory($main_slug, $column, $orderBy, $page);
        } elseif ($main_slug == null && $sub_slug != null && $child_slug == null) {
            return Category::getProductListBySubCategory($sub_slug, $column, $orderBy, $page);
        } elseif ($main_slug == null && $sub_slug != null && $child_slug != null) {
            return Category::getProductListByChildCategory($child_slug, $column, $orderBy, $page);
        }
    }

    public static function getProductListByMainCategory($slug, $column, $orderBy, $page=null,$brands=null,$guaranties=null,$colors=null)
    {
        $categoryList = [];
        $category = Category::query()->where('slug', $slug)->first();
        if (sizeof($category->childCategory) > 0) {
            foreach ($category->childCategory as $category1) {
                if (sizeof($category1->childCategory) > 0) {
                    foreach ($category1->childCategory()->get() as $category2) {
                        array_push($categoryList, $category2->id);
                    }
                }
            }
        }

        if ($page){
            return Product::query()->whereIn('category_id', $categoryList)
                ->when($brands,function ($q) use ($brands){
                    $q->whereIn('brand_id',$brands);
                })
                ->when($guaranties,function ($q) use ($guaranties){
                    $q->whereHas('productPrices',function ($q2) use ($guaranties){
                        $q2->whereIn('guaranty_id',$guaranties);
                    });
                })
                ->when($colors,function ($q) use ($colors){
                    $q->whereHas('colors',function ($q2) use ($colors){
                        $q2->whereIn('color_id',$colors);
                    });
                })
                ->orderBy($column, $orderBy)->paginate(2, ['*'], 'page', $page);
        }else{
            return Product::query()->whereIn('category_id', $categoryList)
                ->orderBy($column, $orderBy)->get();
        }
    }

    public static function getProductListBySubCategory($slug, $column, $orderBy, $page=null,$brands=null,$guaranties=null,$colors=null)
    {
        $categoryList = [];
        $category = Category::query()->where('slug', $slug)->first();
        if (sizeof($category->childCategory) > 0) {
            foreach ($category->childCategory as $category1) {
                array_push($categoryList, $category1->id);
            }
        }
        if ($page){
            return Product::query()->whereIn('category_id', $categoryList)
                ->when($brands,function ($q) use ($brands){
                    $q->whereIn('brand_id',$brands);
                })
                ->when($guaranties,function ($q) use ($guaranties){
                    $q->whereHas('productPrices',function ($q2) use ($guaranties){
                        $q2->whereIn('guaranty_id',$guaranties);
                    });
                })
                ->when($colors,function ($q) use ($colors){
                    $q->whereHas('colors',function ($q2) use ($colors){
                        $q2->whereIn('color_id',$colors);
                    });
                })
                ->orderBy($column, $orderBy)->paginate(2, ['*'], 'page', $page);
        }else{
            return Product::query()->whereIn('category_id', $categoryList)
                ->orderBy($column, $orderBy)->get();
        }
    }

    public static function getProductListByChildCategory($slug, $column, $orderBy, $page=null,$brands=null,$guaranties=null,$colors=null)
    {

        $category = Category::query()->where('slug', $slug)->first();
        if ($page){
            return Product::query()->where('category_id', $category->id)
                ->when($brands,function ($q) use ($brands){
                    $q->whereIn('brand_id',$brands);
                })
                ->when($guaranties,function ($q) use ($guaranties){
                    $q->whereHas('productPrices',function ($q2) use ($guaranties){
                        $q2->whereIn('guaranty_id',$guaranties);
                    });
                })
                ->when($colors,function ($q) use ($colors){
                    $q->whereHas('colors',function ($q2) use ($colors){
                        $q2->whereIn('color_id',$colors);
                    });
                })
                ->orderBy($column, $orderBy)->paginate(2, ['*'], 'page', $page);
        }else{
            return Product::query()->where('category_id', $category->id)
                ->orderBy($column, $orderBy)->get();
        }

    }
}
