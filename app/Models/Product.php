<?php

namespace App\Models;


use App\Enums\CommentStatus;
use App\Enums\DiscountCampaignStatus;
use App\Enums\DiscountCampaignType;
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
        'sold',
        'image',
        'description',
        'main_file',
        'file_size',
        'download_count',
        'status',
        'user_id',
        'category_id',
        'file_extension'
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

    public function campaignTargets()
    {
        return $this->hasMany(DiscountCampaignTarget::class, 'target_id');
    }

    /**
     * دسترسی مستقیم به خودِ کمپین‌ها از طریق جدول واسط
     */
    public function campaigns()
    {
        return $this->hasManyThrough(
            DiscountCampaign::class,
            DiscountCampaignTarget::class,
            'target_id',   // کلید خارجی در جدول واسط
            'id',          // کلید اصلی در جدول کمپین
            'id',          // کلید اصلی در جدول محصول
            'campaign_id'  // کلید خارجی در جدول واسط که به کمپین وصل است
        );
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

            // ایجاد محصول بدون ذخیره قیمت نهایی (چون داینامیک است)
            $product = self::create([
                'user_id' => auth()->id(),
                'category_id' => $request->input('category_id'),
                'name' => $request->input('name'),
                'e_name' => $request->input('e_name'),
                'slug' => $slug,
                'description' => $request->input('description'),
                'main_price' => $request->input('main_price', 0),
                'image' => $request->image ? ImageManager::saveProductImage('products', $request->image) : null,
                'main_file' => FileManager::saveDigitalFile($mainFile, $slug),
                'file_size' => $mainFile ? $mainFile->getSize() : 0,
                'file_extension' => $mainFile ? $mainFile->getClientOriginalExtension() : null,
            ]);

            // اگر درصد تخفیف وارد شده باشد، یک کمپین اختصاصی برای این محصول بساز
            if ($request->filled('discount') && $request->discount > 0) {
                $campaign = DiscountCampaign::create([
                    'name' => "تخفیف محصول: " . $product->name,
                    'type' => DiscountCampaignType::Product->value,
                    'percent' => $request->discount,
                    'priority' => 3, // بالاترین اولویت برای محصول
                    'starts_at' => $request->filled('spacial_start') ? DateManager::shamsi_to_miladi($request->spacial_start) : now(),
                    'expires_at' => $request->filled('spacial_expiration') ? DateManager::shamsi_to_miladi($request->spacial_expiration) : null,
                ]);

                // اتصال کمپین به محصول در جدول واسط هوشمند
                $campaign->targets()->create(['target_id' => $product->id]);
            }

            if ($request->filled('tags')) {
                $product->tags()->attach($request->tags);
            }

            return $product;
        });
    }

    public static function updateProduct($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $product = self::findOrFail($id);
            $slug = str($request->e_name)->slug('-', null);

            // ۱. مدیریت تصویر
            $imageName = $product->image;
            if ($request->hasFile('image')) {
                ImageManager::unlinkImage('products', $product);
                $imageName = ImageManager::saveProductImage('products', $request->image);
            }

            // ۲. مدیریت فایل دیجیتال
            if ($request->hasFile('main_file')) {
                FileManager::deleteDigitalFile($product->slug, $product->main_file);
                $newFile = $request->file('main_file');
                $product->main_file = FileManager::saveDigitalFile($newFile, $slug);
                $product->file_size = $newFile->getSize();
                $product->file_extension = $newFile->getClientOriginalExtension();
            }

            // ۳. آپدیت اطلاعات پایه
            $product->update([
                'name' => $request->input('name'),
                'e_name' => $request->input('e_name'),
                'slug' => $slug,
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'main_price' => $request->input('main_price', 0),
                'image' => $imageName,
            ]);

            // ۴. مدیریت تخفیف (آپدیت یا ایجاد کمپین اختصاصی محصول)
            if ($request->filled('discount')) {
                // پیدا کردن کمپین قبلی محصول (اگر وجود داشته باشد)
                $existingCampaignTarget = DiscountCampaignTarget::where('target_id', $product->id)
                    ->whereHas('campaign', function ($query) {
                        $query->where('type', DiscountCampaignType::Product->value);
                    })->first();

                $campaignData = [
                    'percent' => $request->discount,
                    'starts_at' => $request->filled('spacial_start') ? DateManager::shamsi_to_miladi($request->spacial_start) : now(),
                    'expires_at' => $request->filled('spacial_expiration') ? DateManager::shamsi_to_miladi($request->spacial_expiration) : null,
                ];

                if ($existingCampaignTarget) {
                    $existingCampaignTarget->campaign->update($campaignData);
                } elseif ($request->input('discount') > 0) {
                    $newCampaign = DiscountCampaign::create(array_merge($campaignData, [
                        'name' => "تخفیف محصول: " . $product->name,
                        'type' => DiscountCampaignType::Product->value,
                        'priority' => 3
                    ]));
                    $newCampaign->targets()->create(['target_id' => $product->id]);
                }
            }

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

    /**
     * اکسسور قیمت نهایی - تمام منطق اینجا مدیریت می‌شود
     */
    public function getFinalPriceAttribute()
    {
        $now = now();

        $bestCampaign = DiscountCampaign::where('status', DiscountCampaignStatus::Active->value)
            ->where('starts_at', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->where(function ($query) {
                $query->whereHas('targets', function ($q) {
                    $q->where('target_id', $this->id);
                })
                    ->orWhereHas('targets', function ($q) {
                        $q->where('target_id', $this->category_id);
                    })
                    ->orWhere('type', DiscountCampaignType::Global->value);
            })
            ->orderByDesc('priority')
            ->orderByDesc('percent')
            ->first();

        if ($bestCampaign) {
            $discountAmount = ($this->main_price * $bestCampaign->percent) / 100;
            return $this->main_price - $discountAmount;
        }

        return $this->main_price;
    }

    /**
     * متد کمکی برای چک کردن وجود تخفیف
     * حالا خیلی ساده از ویژگی بالا استفاده می‌کند
     */
    public function hasDiscount()
    {
        // استفاده از ویژگی final_price که توسط اکسسور بالا ساخته شده
        return $this->final_price < $this->main_price;
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
