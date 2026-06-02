<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'image',
        'phone',
        'telegram',
        'eta',
        'instagram',
        'website',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
