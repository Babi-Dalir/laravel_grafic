<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStar extends Model
{
    protected $fillable = [
        'name',
        'product_id',
        'score',
        'count',
    ];
}
