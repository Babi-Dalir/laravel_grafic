<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVote extends Model
{
    protected $fillable = [
        'user_id',
        'comment_id',
        'type',
        'vote_type',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
