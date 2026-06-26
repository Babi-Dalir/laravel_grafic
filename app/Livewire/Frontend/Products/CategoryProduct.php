<?php

namespace App\Livewire\Frontend\Products;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProduct extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $main_slug;
    public $sub_slug;
    public $child_slug;

    // 🟢 بازگرداندن متغیر page فیزیکی برای هماهنگی با قالب پجینیشن شما
    public $page = 1;
    public $sort = 'all';
    public $compare_product_list = [];
    public $currentCategory;

    public function mount()
    {
        $this->currentCategory = Category::getCategoryBySlug(
            $this->main_slug,
            $this->sub_slug,
            $this->child_slug
        );
    }

    /**
     * 🟢 حل قطعی خطا: بازگرداندن متد اصلی مورد نیاز قالب پجینیشن اختصاصی شما
     * این متد توسط قالب‌های محصولات (all-products, newest-products, more-sold-products) صدا زده می‌شود.
     */
    public function changePage($page, $index)
    {
        $this->page = $page;

        // تعیین نوع ستون بر اساس ایندکس ارسالی از پجینیشن شما
        $this->sort = match ((int)$index) {
            1 => 'all',
            2 => 'newest',
            3 => 'more_sold',
            default => 'all',
        };

        // تنظیم صفحه در سیستم داخلی لایووایر بر اساس نام پجینیشن تب
        $this->setPage($page, $this->getPaginationName());
    }

    public function allProducts()
    {
        $this->sort = 'all';
        $this->page = 1;
        $this->resetPage($this->getPaginationName());
    }

    public function newestProducts()
    {
        $this->sort = 'newest';
        $this->page = 1;
        $this->resetPage($this->getPaginationName());
    }

    public function moreSoldProducts()
    {
        $this->sort = 'more_sold';
        $this->page = 1;
        $this->resetPage($this->getPaginationName());
    }

    public function compareProducts($product_id)
    {
        if (!in_array($product_id, $this->compare_product_list)) {
            $this->compare_product_list[] = $product_id;
        } else {
            $this->compare_product_list = array_diff($this->compare_product_list, [$product_id]);
        }

        if (count($this->compare_product_list) === 2) {
            return redirect()->route('compare.products', [
                $this->compare_product_list[0],
                $this->compare_product_list[1]
            ]);
        }
    }

    /**
     * نام مستعار پجینیشن برای تفکیک تب‌ها در مروگر
     */
    private function getPaginationName()
    {
        return match ($this->sort) {
            'newest'    => 'newest_page',
            'more_sold' => 'sold_page',
            default     => 'all_page',
        };
    }

    public function render()
    {
        // تنظیم ستون‌های مرتب‌سازی منطبق بر دیتابیس و متدهای مدل شما
        [$orderByColumn, $direction] = match ($this->sort) {
            'newest'    => ['created_at', 'DESC'],
            'more_sold' => ['sold', 'DESC'],
            default     => ['id', 'DESC'],
        };

        $paginationName = $this->getPaginationName();

        // دریافت اتوماتیک صفحه از دیتای داخلی لایووایر
        $currentPage = $this->paginators[$paginationName] ?? $this->page;

        // فراخوانی متد مدل با ساختار اصلی پروژه شما
        $products = Category::getProductByCategory(
            $this->main_slug,
            $this->sub_slug,
            $this->child_slug,
            $orderByColumn,
            $direction,
            $currentPage,
            $paginationName
        );

        return view('livewire.frontend.products.category-product', [
            'products' => $products
        ]);
    }
}
