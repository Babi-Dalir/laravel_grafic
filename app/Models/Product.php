<?php

namespace App\Models;

use App\Enums\CommentStatus;
use App\Enums\DiscountCampaignStatus;
use App\Enums\DiscountCampaignType;
use App\Enums\ProductStatus;
use App\Helpers\DateManager;
use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        'download_count',
        'status',
        'review_note',
        'user_id',
        'category_id',
    ];

    protected static function booted()
    {
        $clearHomeCache = function () {
            Cache::forget('home.products.most_sold');
            Cache::forget('home.products.newest_products');
            Cache::forget('home.products.special');
            Cache::forget('home.products.instant_offers');
        };

        static::saved($clearHomeCache);
        static::deleted($clearHomeCache);
    }

    protected $appends = ['final_price', 'discount_percent', 'completion_percent'];

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
        return $this->hasMany(DiscountCampaignTarget::class, 'target_id')
            ->where('target_type', DiscountCampaignType::Product->value);
    }

    public function campaigns()
    {
        return $this->hasManyThrough(
            DiscountCampaign::class,
            DiscountCampaignTarget::class,
            'target_id',
            'id',
            'id',
            'discount_campaign_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function downloads()
    {
        return $this->hasMany(Downloads::class);
    }

    public function files()
    {
        return $this->hasMany(ProductFile::class);
    }

    public function mainFile()
    {
        return $this->hasOne(ProductFile::class)->where('is_default', true);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'user_id', 'user_id');
    }

    public static function createProduct($request)
    {
        return DB::transaction(function () use ($request) {
            $slug = str()->slug($request->e_name, '-', null);

            $product = self::create([
                'user_id' => auth()->id(),
                'category_id' => $request->input('category_id'),
                'name' => $request->input('name'),
                'e_name' => $request->input('e_name'),
                'slug' => $slug,
                'description' => $request->input('description'),
                'main_price' => $request->input('main_price', 0),
                'image' => $request->image ? ImageManager::saveProductImage('products', $request->image) : null,
                'status' => auth()->user()->hasRole('مدیر')
                    ? ProductStatus::Approved->value
                    : ProductStatus::PendingReview->value,
            ]);

            if ($request->filled('discount') && $request->discount > 0) {
                $campaign = DiscountCampaign::create([
                    'name' => "تخفیف محصول: " . $product->name,
                    'type' => DiscountCampaignType::Product->value,
                    'percent' => $request->discount,
                    'priority' => 3,
                    'starts_at' => $request->filled('spacial_start') ? DateManager::shamsi_to_miladi($request->spacial_start) : now(),
                    'expires_at' => $request->filled('spacial_expiration') ? DateManager::shamsi_to_miladi($request->spacial_expiration) : null,
                ]);

                $campaign->targets()->create([
                    'target_id' => $product->id,
                    'target_type' => DiscountCampaignType::Product->value
                ]);
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
            $slug = str()->slug($request->e_name, '-', null);

            $imageName = $product->image;
            if ($request->hasFile('image')) {
                ImageManager::unlinkImage('products', $product);
                $imageName = ImageManager::saveProductImage('products', $request->image);
            }

            $product->update([
                'name' => $request->input('name'),
                'e_name' => $request->input('e_name'),
                'slug' => $slug,
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'main_price' => $request->input('main_price', 0),
                'image' => $imageName,
                'review_note' => auth()->user()->hasRole('مدیر') ? $product->review_note : null,
                'status' => auth()->user()->hasRole('مدیر')
                    ? ProductStatus::Approved->value
                    : ProductStatus::PendingReview->value,
            ]);

            // ⚡ مدیریت دقیق و بدون نقص ویرایش تخفیف و تاریخ‌های شگفت‌انگیز کمپین محصول
            $existingCampaignTarget = DiscountCampaignTarget::where('target_id', $product->id)
                ->where('target_type', DiscountCampaignType::Product->value)
                ->whereHas('campaign', function ($query) {
                    $query->where('type', DiscountCampaignType::Product->value);
                })
                ->first();

            // آماده‌سازی آرایه داده‌ها با تبدیل به تاریخ میلادی
            $campaignData = [
                'percent' => $request->input('discount', 0),
                'starts_at' => $request->filled('spacial_start') ? DateManager::shamsi_to_miladi($request->spacial_start) : now(),
                'expires_at' => $request->filled('spacial_expiration') ? DateManager::shamsi_to_miladi($request->spacial_expiration) : null,
            ];

            if ($request->filled('discount') && $request->input('discount') > 0) {
                if ($existingCampaignTarget && $existingCampaignTarget->campaign) {
                    // اگر کمپین از قبل وجود دارد، آن را بروزرسانی کن
                    $existingCampaignTarget->campaign->update($campaignData);
                } else {
                    // اگر قبلاً تخفیف نداشت ولی الان مقدار وارد شده، کمپین جدید بساز
                    $newCampaign = DiscountCampaign::create(array_merge($campaignData, [
                        'name' => "تخفیف محصول: " . $product->name,
                        'type' => DiscountCampaignType::Product->value,
                        'priority' => 3
                    ]));
                    $newCampaign->targets()->create([
                        'target_id' => $product->id,
                        'target_type' => DiscountCampaignType::Product->value
                    ]);
                }
            } else {
                // 💡 نکته طلایی: اگر کاربر درصد تخفیف را خالی یا صفر گذاشت، کمپین اختصاصی قبلی حذف شود
                if ($existingCampaignTarget && $existingCampaignTarget->campaign) {
                    $campaign = $existingCampaignTarget->campaign;
                    $existingCampaignTarget->delete();
                    $campaign->delete();
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

        static::deleting(function ($product) {
            if ($product->isForceDeleting()) {
                ImageManager::unlinkImage('products', $product);
                Storage::disk('digital_files')->deleteDirectory("products/{$product->id}");
            }
        });
    }

    public function getCompletionPercentAttribute()
    {
        $items = $this->reviewChecklist();
        $total = count($items);
        $completed = collect($items)->filter()->count();

        return intval(($completed / $total) * 100);
    }

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
                    $q->where('target_id', $this->id)->where('target_type', DiscountCampaignType::Product->value);
                })
                    ->orWhereHas('targets', function ($q) {
                        $q->where('target_id', $this->category_id)->where('target_type', DiscountCampaignType::Category->value);
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

    public function getDiscountPercentAttribute()
    {
        if ($this->main_price > 0 && $this->final_price < $this->main_price) {
            $diff = $this->main_price - $this->final_price;
            return round(($diff / $this->main_price) * 100);
        }
        return 0;
    }

    public function hasDiscount()
    {
        return $this->final_price < $this->main_price;
    }

    public function scopeSmartOffer($query)
    {
        $now = now();
        // وضعیت تایید شده از Active به Approved (مطابق با متد کامپوننت لایووایر شما) اصلاح شد
        return $query->where('status', ProductStatus::Approved->value)
            ->where(function ($q) use ($now) {
                $q->whereHas('campaignTargets.campaign', function ($sub) use ($now) {
                    $sub->where('status', DiscountCampaignStatus::Active->value)
                        ->where('starts_at', '<=', $now)
                        ->where(function ($e) use ($now) {
                            $e->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                        });
                })
                    ->orWhereHas('category.campaignTargets.campaign', function ($sub) use ($now) {
                        $sub->where('status', DiscountCampaignStatus::Active->value)
                            ->where('starts_at', '<=', $now)
                            ->where(function ($e) use ($now) {
                                $e->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                            });
                    })
                    ->orWhereExists(function ($sub) use ($now) {
                        $sub->select(DB::raw(1))
                            ->from('discount_campaigns')
                            ->where('type', DiscountCampaignType::Global->value)
                            ->where('status', DiscountCampaignStatus::Active->value)
                            ->where('starts_at', '<=', $now)
                            ->where(function ($e) use ($now) {
                                $e->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                            });
                    });
            })
            ->latest()
            ->limit(9);
    }

    public function isReadyForReview(): bool
    {
        return collect($this->reviewChecklist())->every(fn($item) => $item);
    }

    public function submitForReview()
    {
        if (!$this->isReadyForReview()) {
            return $this->reviewErrors();
        }

        $this->update([
            'status' => auth()->user()->hasRole('مدیر')
                ? ProductStatus::Approved->value
                : ProductStatus::PendingReview->value
        ]);

        return true;
    }

    public function reviewChecklist(): array
    {
        return [
            'image' => !empty($this->image),
            'description' => !empty($this->description),
            'gallery' => $this->galleries()->exists(),
            'properties' => $this->propertyGroups()->exists(),
            'files' => $this->files()->exists(),
            'price' => $this->main_price > 0,
        ];
    }

    public function reviewErrors(): array
    {
        return collect($this->reviewChecklist())
            ->filter(fn ($value) => !$value)
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => $this->errorMessage($key)])
            ->toArray();
    }

    public function errorMessage($key): string
    {
        return match ($key) {
            'image' => 'تصویر محصول ثبت نشده است',
            'description' => 'توضیحات محصول تکمیل نشده است',
            'gallery' => 'حداقل یک تصویر در گالری لازم است',
            'properties' => 'ویژگی‌های محصول تعریف نشده است',
            'files' => 'حداقل یک فایل باید آپلود شود',
            'price' => 'قیمت محصول مشخص نیست',
            default => 'اطلاعات ناقص است',
        };
    }

    public function getSellerAttribute()
    {
        return $this->user?->seller;
    }
}
