<?php

namespace App\Livewire\Seller\SellerProducts;

use App\Models\Property;
use App\Models\PropertyGroup;
use Livewire\Attributes\On;
use Livewire\Component;

class SellerProductPropertyList extends Component
{
    public $product;
    public $property_group_id;
    public $name;

    public function mount($product)
    {
        // 🔒 گارد امنیتی: اگر فروشنده‌ای خواست مستقیماً با آدرس URL وارد صفحه ویژگی محصول فروشنده دیگری شود، مسدود شود.
        if ($product->user_id !== auth()->id()) {
            abort(403, 'شما اجازه دسترسی به این محصول را ندارید.');
        }
        $this->product = $product;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|min:2',
            'property_group_id' => 'required|exists:property_groups,id',
        ], [
            'name.required' => 'نام ویژگی الزامی است',
            'property_group_id.required' => 'انتخاب گروه ویژگی الزامی است',
        ]);

        // بررسی وجود رابطه گروه ویژگی با محصول به صورت امن
        $exist = $this->product->propertyGroups()
            ->where('property_group_id', $this->property_group_id)
            ->exists();

        if (!$exist) {
            $this->product->propertyGroups()->attach($this->property_group_id);
        }

        // ثبت ویژگی جدید با قفل روی محصول جاری
        Property::query()->create([
            'name' => trim($this->name),
            'property_group_id' => $this->property_group_id,
            'product_id' => $this->product->id
        ]);

        $this->reset(['name', 'property_group_id']);
        session()->flash('message', 'ویژگی با موفقیت اضافه شد.');
    }

    #[On('destroy_seller_product_property_group')]
    public function destroyProductPropertyGroup($property_group_id)
    {
        // 🔒 احراز اصالت محصول: اطمینان از اینکه محصول هنوز مال همین کاربر است
        if ($this->product->user_id !== auth()->id()) {
            return;
        }

        $exist = $this->product->propertyGroups()
            ->where('property_group_id', $property_group_id)
            ->exists();

        if ($exist) {
            // 🔒 امنیت دیتابیس: فقط ویژگی‌های مربوط به همین محصول حذف شوند (نه کل سایت!)
            Property::query()
                ->where('product_id', $this->product->id)
                ->where('property_group_id', $property_group_id)
                ->delete();

            $this->product->propertyGroups()->detach($property_group_id);
            $this->dispatch('propertyDeletedSuccess');
        }
    }

    #[On('destroy_seller_product_property')]
    public function destroyProductProperty($property_group_id, $property_id)
    {
        // 🔒 احراز اصالت محصول
        if ($this->product->user_id !== auth()->id()) {
            return;
        }

        // حذف ایمن ویژگی با شرط داشتن کلید محصول جاری
        Property::query()
            ->where('id', $property_id)
            ->where('product_id', $this->product->id)
            ->delete();

        // چک کردن اینکه آیا ویژگی دیگری از این گروه برای این محصول باقی مانده یا خیر
        $remainExist = Property::query()
            ->where('property_group_id', $property_group_id)
            ->where('product_id', $this->product->id)
            ->exists();

        if (!$remainExist) {
            $this->product->propertyGroups()->detach($property_group_id);
        }

        $this->dispatch('propertyDeletedSuccess');
    }

    public function render()
    {
        $category = $this->product->category;
        $validCategoryIds = [];

        if ($category && $category->parent_id != 0) {
            $validCategoryIds[] = $category->id;
            $parent = $category->parentCategory;
            if ($parent && $parent->parent_id != 0) {
                $validCategoryIds[] = $parent->id;
            }
        }

        // گروه‌های ویژگی مجاز برای این دسته‌بندی
        $property_groups = PropertyGroup::query()
            ->whereIn('category_id', $validCategoryIds)
            ->get();

        // 🚀 رفع باگ N+1: لود گروه‌ها به همراه فیلتر ویژگی‌های اختصاصی همین محصول در یک کوئری یکپارچه
        $product_property_groups = $this->product->propertyGroups()
            ->with(['properties' => function ($query) {
                $query->where('product_id', $this->product->id);
            }])
            ->get();

        return view('livewire.seller.seller-products.seller-product-property-list', compact('property_groups', 'product_property_groups'));
    }
}
