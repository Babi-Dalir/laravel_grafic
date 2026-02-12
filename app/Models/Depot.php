<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Depot extends Model
{
    protected $fillable = [
        'name',
        'address',
        'status',

    ];
    public static function createDepot($request)
    {
        Depot::query()->create([
            'name'=>$request->input('name'),
            'address'=>$request->input('address'),
        ]);
    }
    public static function updateDepot($request,$id)
    {
        $depot = Depot::query()->find($id);
        $depot->update([
            'name'=>$request->input('name'),
            'address'=>$request->input('address'),
        ]);
    }
}
