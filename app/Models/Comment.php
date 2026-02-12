<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'name',
        'advantage',
        'disadvantage',
        'is_buyer',
        'suggestion',
        'like',
        'dislike',
        'body',
        'commentable_id',
        'commentable_type',
        'status',

    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
