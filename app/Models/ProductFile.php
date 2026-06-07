<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    protected $fillable = [

        'product_id',
        'title',
        'original_name',
        'stored_name',
        'extension',
        'mime_type',
        'size',
        'sha256',
        'is_default',
    ];

    protected $casts = [

        'is_default' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getPathAttribute()
    {
        return "products/{$this->product_id}/{$this->stored_name}";
    }
}
