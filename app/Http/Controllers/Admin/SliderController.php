<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "لیست اسلایدر ها";
        return view('admin.sliders.list', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "ایجاد اسلایدر";
        return view('admin.sliders.create',compact('title'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Slider::createSlider($request);
        return redirect()->route('sliders.index')->with('message', 'اسلایدر جدید با موفقیت ایجاد شد');
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
        $title = "ویرایش اسلایدر";
        $slider = Slider::query()->find($id);
        return view('admin.sliders.edit', compact('slider','title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Slider::updateSlider($request,$id);
        return redirect()->route('sliders.index')->with('message', 'اسلایدر جدید با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
