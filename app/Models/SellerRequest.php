<?php

namespace App\Models;

use App\Enums\SellerRequestStatus;
use App\Helpers\FileManager;
use Illuminate\Database\Eloquent\Model;

class SellerRequest extends Model
{
    protected $fillable = [
        'user_id',
        'brand_name',
        'portfolio',
        'resume',
        'reason',
        'status',
        'reviewed_at',
        'admin_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createSellerRequest($request)
    {
        $user = auth()->user();
        $resume = null;
        if ($request->hasFile('resume')) {
            $resume = FileManager::saveResume(
                $request->file('resume'),
                $user->id
            );
        }

        return SellerRequest::query()->create([
            'user_id' => $user->id,
            'brand_name' => $request->brand_name,
            'portfolio' => $request->portfolio,
            'resume' => $resume,
            'reason' => $request->reason,
        ]);
    }
}
