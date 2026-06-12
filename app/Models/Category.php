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

    public function campaignTargets()
    {
        return $this->hasMany(DiscountCampaignTarget::class, 'target_id');
    }

    public static function createCategory($request)
    {
        Category::query()->create([
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->input('e_name')),
            'image' => ImageManager::saveImage('categories', $request->image),
            'parent_id' => $request->input('parent_id'),

        ]);
    }

    public static function updateCategory($request, $category)
    {
        if ($request->hasFile('image')) {
            ImageManager::unlinkImage('categories', $category); // حذف عکس قبلی
            $imageName = ImageManager::saveImage('categories', $request->image);
        }
        $category->update([
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->input('e_name')),
            'image' => $request->image ? $imageName : $category->image,
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
    public static function getLeafCategories()
    {
        return self::query()
            ->doesntHave('childCategory')
            ->pluck('name','id')
            ->toArray();
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
            // ۱. مدیریت زیرمجموعه‌ها (فرزندان)
            foreach ($category->childCategory()->withTrashed()->get() as $cat) {
                if ($category->isForceDeleting()) {
                    $cat->forceDelete();
                } else {
                    $cat->delete();
                }
            }
            // ۲. مدیریت حذف فیزیکی عکس (فقط در حذف دائمی)
            if ($category->isForceDeleting()) {
                ImageManager::unlinkImage('categories', $category);
            }
        });
        self::restoring(function ($category) {
            // بازگردانی زیرمجموعه‌ها
            foreach ($category->childCategory()->withTrashed()->get() as $cat) {
                $cat->restore();
            }
        });
    }
    public static function getCategoryBySlug($main_slug, $sub_slug, $child_slug)
    {
        if ($main_slug) {
            return self::where('slug', $main_slug)->first();
        }

        if ($child_slug) {
            return self::where('slug', $child_slug)->first();
        }

        return self::where('slug', $sub_slug)->first();
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

    public static function getProductListByMainCategory($slug, $column, $orderBy, $page = null)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $categoryIds = [$category->id];

        foreach ($category->childCategory as $child) {

            $categoryIds[] = $child->id;

            foreach ($child->childCategory as $grandChild) {
                $categoryIds[] = $grandChild->id;
            }
        }

        $query = Product::query()
            ->whereIn('category_id', $categoryIds)
            ->orderBy($column, $orderBy);

        return $page
            ? $query->paginate(20, ['*'], 'page', $page)
            : $query->get();
    }

    public static function getProductListBySubCategory($slug, $column, $orderBy, $page = null)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $categoryIds = [$category->id];

        foreach ($category->childCategory as $child) {
            $categoryIds[] = $child->id;
        }

        $query = Product::query()
            ->whereIn('category_id', $categoryIds)
            ->orderBy($column, $orderBy);

        return $page
            ? $query->paginate(20, ['*'], 'page', $page)
            : $query->get();
    }

    public static function getProductListByChildCategory($slug, $column, $orderBy, $page = null)
    {
        $category = Category::query()->where('slug', $slug)->first();
        if ($page) {
            return Product::query()->where('category_id', $category->id)
                ->orderBy($column, $orderBy)->paginate(2, ['*'], 'page', $page);
        } else {
            return Product::query()->where('category_id', $category->id)
                ->orderBy($column, $orderBy)->get();
        }
    }
}
