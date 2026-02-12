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
            $product = Product::query()->create([
                'user_id' => auth()->user()->id,
                'name' => $request->input('name'),
                'e_name' => $request->input('e_name'),
                'slug' => str()->slug($request->e_name),
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'brand_id' => $request->input('brand_id'),
                'image' => ImageManager::saveImage('products', $request->image),
            ]);
            $product->tags()->attach($request->tags);
            ProductPrice::createProductPrice($request,$product->id);

            DB::commit();

        }catch (\Exception $exception) {

            DB::rollBack();
        }

    }
}
