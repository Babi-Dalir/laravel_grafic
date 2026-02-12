<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($product_id)
    {
        $title = "لیست نقد و برسی";
        return view('admin.reviews.list', compact('title','product_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($product_id)
    {
        $title = "ایجاد نقد و برسی";
        return view('admin.reviews.create',compact('title','product_id'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$product_id)
    {
        Review::createReview($request,$product_id);
        return redirect()->route('product.reviews',$product_id)->with('message', 'نقد و برسی جدید با موفقیت ایجاد شد');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id,$product_id)
    {
        $title = "ویرایش نقد و برسی";
        $review = Review::query()->find($id);
        return view('admin.reviews.edit', compact('review','title','product_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id,$product_id)
    {
        Review::updateReview($request,$id,$product_id);
        return redirect()->route('product.reviews',$product_id)->with('message', 'نقد و برسی با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
