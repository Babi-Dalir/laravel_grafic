<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Property;
use App\Models\PropertyGroup;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductPropertyList extends Component
{
    public $product;
    public $property_group_id;
    public $name;

    public function submit()
    {
        $this->validate([
            'name' => 'required|min:2',
            'property_group_id' => 'required|exists:property_groups,id',
        ], [
            'name.required' => 'نام ویژگی الزامی است',
            'property_group_id.required' => 'انتخاب گروه ویژگی الزامی است',
        ]);

        $propertyExists = Property::query()
            ->where('product_id', $this->product->id)
            ->where('property_group_id', $this->property_group_id)
            ->where('name', $this->name)
            ->exists();

        if ($propertyExists) {
            // ارسال پیام خطای اتمیک به ادمین پلتفرم
            session()->flash('error', 'این ویژگی با همین مقدار قبلاً برای این محصول ثبت شده است.');
            return;
        }

        // ادامه کدهای اصلی شما بدون هیچ تغییری:
        $exist = $this->product->propertyGroups()
            ->where('property_group_id', $this->property_group_id)
            ->exists();

        if (!$exist) {
            $this->product->propertyGroups()->attach($this->property_group_id);
        }

        Property::query()->create([
            'name' => $this->name,
            'property_group_id' => $this->property_group_id,
            'product_id' => $this->product->id
        ]);

        $this->reset(['name', 'property_group_id']);
        session()->flash('message', 'ویژگی با موفقیت اضافه شد.');
    }

    #[On('destroy_product_property_group')]
    public function destroyProductPropertyGroup($property_group_id)
    {
        $exist = $this->product->propertyGroups()
            ->where('property_group_id', $property_group_id)
            ->exists();

        if ($exist) {
            // 🟢 بهینه‌سازی آنلاین و امنیتی: فقط ویژگی‌های مربوط به همین محصول حذف شوند، نه بقیه محصولات!
            Property::query()
                ->where('property_group_id', $property_group_id)
                ->where('product_id', $this->product->id)
                ->delete();

            $this->product->propertyGroups()->detach($property_group_id);
        }
    }

    #[On('destroy_product_property')]
    public function destroyProductProperty($property_group_id, $property_id)
    {
        Property::destroy($property_id);

        $exist = Property::query()
            ->where('property_group_id', $property_group_id)
            ->where('product_id', $this->product->id)
            ->exists();

        if (!$exist) {
            $this->product->propertyGroups()->detach($property_group_id);
        }
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

        $property_groups = PropertyGroup::query()
            ->whereIn('category_id', $validCategoryIds)
            ->get();

        // 🟢 بهینه‌سازی آنلاین: لود کردن ویژگی‌های مرتبط با همین محصول به صورت کاملاً Eager Load جهت حل باگ N+1
        $product_property_groups = $this->product->propertyGroups()
            ->with(['properties' => function($query) {
                $query->where('product_id', $this->product->id);
            }])
            ->get();

        return view('livewire.admin.products.product-property-list', compact('property_groups', 'product_property_groups'));
    }
}
