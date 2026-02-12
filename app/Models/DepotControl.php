<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepotControl extends Model
{
    protected $fillable = [
        'user_id',
        'depot_id',
        'product_price_id',
        'count',
        'event_type',

    ];
}
