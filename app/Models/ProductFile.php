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

    public function getHumanSizeAttribute()
    {
        $bytes = $this->size;

        if ($bytes <= 0) {
            return '0 B';
        }

        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
