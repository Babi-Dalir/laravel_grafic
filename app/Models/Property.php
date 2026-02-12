<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'name',
        'product_id',
        'property_group_id',

    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function propertyGroup()
    {
        return $this->belongsTo(PropertyGroup::class);
    }
}
