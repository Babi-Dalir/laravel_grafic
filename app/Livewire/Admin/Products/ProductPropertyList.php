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
        $exist = $this->product->whereHas('propertyGroups', function ($q) {
            $q->where('property_group_id', $this->property_group_id)->where('product_id', $this->product->id);
        })->exists();
        if (!$exist) {
            $this->product->propertyGroups()->attach($this->property_group_id);
        }
        Property::query()->create([
            'name' => $this->name,
            'property_group_id' => $this->property_group_id,
            'product_id' => $this->product->id
        ]);
    }

    #[On('destroy_product_property_group')]
    public function destroyProductPropertyGroup($property_group_id)
    {
        $exist = $this->product->whereHas('propertyGroups', function ($q) use ($property_group_id) {
            $q->where('property_group_id', $property_group_id)->where('product_id', $this->product->id);
        })->exists();
        if ($exist) {
            $properties = PropertyGroup::query()->find($property_group_id)->properties;
            foreach ($properties as $property) {
                $property->delete();
            }
            $this->product->propertyGroups()->detach($property_group_id);
        }
    }

    #[On('destroy_product_property')]
    public function destroyProductProperty($property_group_id,$property_id)
    {
        Property::destroy($property_id);
        $exist = Property::query()->where('property_group_id',$property_group_id)->where('product_id',$this->product->id)->first();
        if (!$exist){
            $this->product->propertyGroups()->detach($property_group_id);
        }
    }

    public function render()
    {
        $property_groups = PropertyGroup::query()
            ->where('category_id', $this->product->category->parentCategory->id)
            ->get();
        $product_property_groups = collect($this->product->propertyGroups);
        return view('livewire.admin.products.product-property-list', compact('property_groups', 'product_property_groups'));
    }
}
