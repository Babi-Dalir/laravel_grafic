<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

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

    /**
     * 🚀 واکشی مستقیم تمام محصولات زیرمجموعه‌ها برای دسته‌های اصلی
     */
    public function subProducts()
    {
        // پارامتر اول: مدل مقصد (محصول)
        // پارامتر دوم: مدل واسط (دسته‌بندی برای ارتباط والد و فرزندی)
        return $this->hasManyThrough(
            Product::class,
            Category::class,
            'parent_id', // کلید خارجی در جدول واسط (اشاره به دسته اصلی)
            'category_id', // کلید خارجی در جدول مقصد (اشاره به زیردسته)
            'id', // کلید داخلی در جدول اصلی
            'id'  // کلید داخلی در جدول واسط
        )->where('products.status', ProductStatus::Approved->value);
    }

    public static function createCategory($request)
    {
        Cache::forget('categories');
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
        Cache::forget('categories');
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

    public static function getLayer2Categories(): array
    {
        // واکشی لایه اول همراه با فرزندان مستقیم (لایه دوم)
        return self::query()
            ->with('childCategory')
            ->where('parent_id', 0)
            ->get()
            ->keyBy('name')
            ->map(function ($mainCategory) {
                return $mainCategory->childCategory->pluck('name', 'id')->toArray();
            })
            ->toArray();
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
    //برای کنترلر گروه ویژگی ها
    public static function getLeafCategories()
    {
        return self::query()
            ->doesntHave('childCategory')
            ->pluck('name','id')
            ->toArray();
    }

    //برای کنترلر گروه ویژگی ها
    public static function getLeafCategoriesWithParent()
    {
        return self::query()
            ->doesntHave('childCategory') // فقط برگ‌های نهایی
            ->with('parentCategory')     // بارگذاری والد برای هدر گروه
            ->get()
            ->groupBy(function ($category) {
                return $category->parentCategory ? $category->parentCategory->name : 'دسته‌بندی‌های عمومی';
            });
    }

    //برای کنترلر کمپین ها
    public static function getHierarchicalCategories()
    {
        // واکشی دسته‌های لایه اول همراه با فرزندان (لایه ۲) و نوه‌ها (لایه ۳)
        return self::query()
            ->with('childCategory.childCategory')
            ->where('parent_id', 0)
            ->get();
    }

    protected static function booted()
    {
        $clearCategoryCaches = function () {
            // متلاشی کردن کش منوی هدر و فرم‌های درختی بک‌آند
            Cache::forget('categories');
            Cache::forget('categories.tree.leaf');
        };

        static::saved($clearCategoryCaches);
        static::deleted($clearCategoryCaches);
    }

    public static function getLeafCategoriesInTree(): array
    {
        return Cache::remember('categories.tree.leaf', now()->addDays(10), function () {
            $leafCategories = self::query()
                ->doesntHave('childCategory')
                ->withCount('subProducts') // 🟢 استفاده از متد شمارنده زنجیره‌ای شما
                ->with('parentCategory.parentCategory')
                ->get();

            $tree = [];

            foreach ($leafCategories as $leaf) {
                $mainName = 'دسته‌بندی‌های عمومی';
                $subName = null;

                if ($leaf->parentCategory && $leaf->parentCategory->parent_id != 0) {
                    $mainName = $leaf->parentCategory->parentCategory ? $leaf->parentCategory->parentCategory->name : 'عمومی';
                    $subName = $leaf->parentCategory->name;
                }
                elseif ($leaf->parentCategory) {
                    $mainName = $leaf->parentCategory->name;
                }

                if ($subName) {
                    $tree[$mainName][$subName][] = $leaf;
                } else {
                    $tree[$mainName][] = $leaf;
                }
            }

            return $tree;
        });
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
        if ($child_slug) {
            return self::where('slug', $child_slug)->first();
        }

        if ($sub_slug) {
            return self::where('slug', $sub_slug)->first();
        }

        if ($main_slug) {
            return self::where('slug', $main_slug)->first();
        }

        return null;
    }

    /**
     * 🟢 اصلاح هوشمند تشخیص عمیق‌ترین سطح دسته برای لود محصولات
     */
    public static function getProductByCategory($main_slug, $sub_slug, $child_slug, $column, $orderBy, $page, $paginationName = 'page')
    {
        // وضعیت تایید شده محصولات را هم گارد گذاشتیم تا محصول تایید نشده آنلاین نمایش داده نشود
        if ($child_slug) {
            return self::getProductListByChildCategory($child_slug, $column, $orderBy, $page, $paginationName);
        }

        if ($sub_slug) {
            return self::getProductListBySubCategory($sub_slug, $column, $orderBy, $page, $paginationName);
        }

        if ($main_slug) {
            return self::getProductListByMainCategory($main_slug, $column, $orderBy, $page, $paginationName);
        }
    }

    public static function getProductListByMainCategory($slug, $column, $orderBy, $page = null, $paginationName = 'page')
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
            ->where('status', ProductStatus::Approved->value) // امنیت آنلاین مارکت
            ->orderBy($column, $orderBy);

        return $page
            ? $query->paginate(15, ['*'], $paginationName, $page)
            : $query->get();
    }

    public static function getProductListBySubCategory($slug, $column, $orderBy, $page = null, $paginationName = 'page')
    {
        $category = self::with('childCategory')->where('slug', $slug)->firstOrFail();

        $categoryIds = [$category->id];

        foreach ($category->childCategory as $child) {
            $categoryIds[] = $child->id;
        }

        $query = Product::query()
            ->whereIn('category_id', $categoryIds)
            ->where('status', ProductStatus::Approved->value)
            ->orderBy($column, $orderBy);

        return $page
            ? $query->paginate(15, ['*'], $paginationName, $page)
            : $query->get();
    }

    public static function getProductListByChildCategory($slug, $column, $orderBy, $page = null, $paginationName = 'page')
    {
        $category = self::where('slug', $slug)->firstOrFail();

        $query = Product::query()
            ->where('category_id', $category->id)
            ->where('status', ProductStatus::Approved->value)
            ->orderBy($column, $orderBy);

        // تعداد ۲ پجینیشن شما برای تست بوده، آن را به ۲۰ یا مقدار دلخواه تغییر دهید (یا بگذارید ۲ بماند)
        return $page
            ? $query->paginate(15, ['*'], $paginationName, $page)
            : $query->get();
    }
}
