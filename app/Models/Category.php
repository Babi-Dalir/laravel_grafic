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
        return $this->hasMany(self::class, 'parent_id', 'id')->with('childCategory');
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
        self::query()->create([
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->input('e_name')),
            'image' => $request->hasFile('image') ? ImageManager::saveImage('categories', $request->image) : null,
            'parent_id' => $request->input('parent_id') ?? 0,
        ]);
    }

    public static function updateCategory($request, $category)
    {
        $imageName = $category->image;
        if ($request->hasFile('image')) {
            ImageManager::unlinkImage('categories', $category); // حذف عکس قبلی
            $imageName = ImageManager::saveImage('categories', $request->image);
        }
        $category->update([
            'name' => $request->input('name'),
            'e_name' => $request->input('e_name'),
            'slug' => str()->slug($request->input('e_name')),
            'image' => $request->hasFile('image') ? $imageName : $category->image,
            'parent_id' => $request->input('parent_id') ?? 0,
        ]);
    }

    public static function getCategories()
    {
        $array = [];
        // اضافه شدن با تو در تو برای حل مشکل سرعت و تعداد کوئری‌ها
        $categories = self::query()->with('childCategory.childCategory')->where('parent_id', 0)->get();
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
        $categories = self::query()->with('childCategory.childCategory')->where('parent_id', 0)->get();
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
        $categories = self::query()->with('childCategory.products')->where('parent_id', $id)->get();
        foreach ($categories as $category1) {
            foreach ($category1->childCategory as $category2) {
                $sum += $category2->products->count(); // استفاده از کالکشن به جای کوئری مجدد
            }
        }
        return $sum;
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function ($category) {
            // واکشی همه فرزندان مستقیم و غیر مستقیم برای جلوگیری از جا ماندن لایه‌ها
            $children = $category->childCategory()->withTrashed()->get();
            foreach ($children as $cat) {
                if ($category->isForceDeleting()) {
                    $cat->forceDelete();
                } else {
                    $cat->delete();
                }
            }
            if ($category->isForceDeleting()) {
                ImageManager::unlinkImage('categories', $category);
            }
        });

        self::restoring(function ($category) {
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
            return self::getProductListByMainCategory($main_slug, $column, $orderBy, $page);
        } elseif ($main_slug == null && $sub_slug != null && $child_slug == null) {
            return self::getProductListBySubCategory($sub_slug, $column, $orderBy, $page);
        } elseif ($main_slug == null && $sub_slug != null && $child_slug != null) {
            return self::getProductListByChildCategory($child_slug, $column, $orderBy, $page);
        }
    }

    public static function getProductListByMainCategory($slug, $column, $orderBy, $page = null)
    {
        $category = self::with('childCategory.childCategory')->where('slug', $slug)->firstOrFail();

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
        $category = self::with('childCategory')->where('slug', $slug)->firstOrFail();

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
        $category = self::where('slug', $slug)->firstOrFail();

        $query = Product::query()->where('category_id', $category->id)
            ->orderBy($column, $orderBy);

        return $page
            ? $query->paginate(2, ['*'], 'page', $page)
            : $query->get();
    }
}
