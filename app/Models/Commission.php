<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'category_id',
        'commission_percent',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function createCommission($request)
    {
        return self::query()->create([
            'commission_percent' => (int) $request->input('commission_percent'),
            'category_id'        => (int) $request->input('category_id'),
        ]);
    }

    public static function updateCommission($request, $id)
    {
        $commission = self::query()->findOrFail($id);

        $commission->update([
            'commission_percent' => (int) $request->input('commission_percent'),
            'category_id'        => (int) $request->input('category_id'),
        ]);

        return $commission;
    }
}
