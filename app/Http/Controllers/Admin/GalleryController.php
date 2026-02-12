<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function ckeditorImage(Request $request)
    {
        if ($request->hasFile('upload')){
            $url = ImageManager::ckeditorImage('products',$request->upload);
            return response()->json(['url'=>$url]);
        }
    }
}
