<?php

namespace App\Models;

use App\Helpers\ImageManager;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable=[
        'name',
        'slug'
    ];
    public function products()
    {
        return $this->morphedByMany(Product::class,'taggable');
    }
    public static function createTag($request)
    {
        Tag::query()->create([
            'name'=>$request->input('name'),
            'slug' => str()->slug($request->name, '-', null),
        ]);
    }
    public static function updateTag($request,$id)
    {
        $tag = Tag::query()->find($id);
        $tag->update([
            'name'=>$request->input('name'),
            'slug' => str()->slug($request->name, '-', null),
        ]);
    }
}
