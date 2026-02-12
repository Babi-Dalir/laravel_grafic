<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bank_account_number',
        'bank_card_number',
        'bank_shaba_number',
        'national_code',
        'phone',
        'telegram',
        'instagram',
        'whatsapp',
        'newsletter',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
