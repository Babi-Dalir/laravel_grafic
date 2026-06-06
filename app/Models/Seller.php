<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_economy_code',
        'contract',
        'status',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function createSellerProduct($request)
    {
        DB::beginTransaction();
        try {
            $product = Product::createProduct();

            DB::commit();

        }catch (\Exception $exception) {

            DB::rollBack();
        }

    }
}
