<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\createUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "لیست برندها";
        return view('admin.brands.list', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "ایجاد برند";
        return view('admin.brands.create',compact('title'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Brand::createBrand($request);
        return redirect()->route('brands.index')->with('message', 'برند جدید با موفقیت ایجاد شد');
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
    public function edit(string $id)
    {
        $title = "ویرایش برند";
        $brand = Brand::query()->find($id);
        return view('admin.brands.edit', compact('brand','title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Brand::updateBrand($request,$id);
        return redirect()->route('brands.index')->with('message', 'برند جدید با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
