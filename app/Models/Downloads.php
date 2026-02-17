<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Downloads extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_detail_id',
        'token',
        'download_count',
        'max_download',
        'status',
        'expire_at',
        'ip_address',
        'user_agent',
    ];
}
