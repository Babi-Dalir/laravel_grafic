<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepotProduct extends Model
{
    protected $fillable = [
        'depot_id',
        'product_price_id',
        'count',
    ];

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }
    public function productPrice()
    {
        return $this->belongsTo(ProductPrice::class);
    }
}
