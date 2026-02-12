<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "لیست انبار ها";
        return view('admin.depots.list', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "ایجاد انبار";
        return view('admin.depots.create',compact('title'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Depot::createDepot($request);
        return redirect()->route('depots.index')->with('message', 'انبار جدید با موفقیت ایجاد شد');
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
        $title = "ویرایش انبار";
        $depot = Depot::query()->find($id);
        return view('admin.depots.edit', compact('depot','title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Depot::updateDepot($request,$id);
        return redirect()->route('depots.index')->with('message', 'انبار با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function addProductInDepot($depot_id)
    {
        $title = "افزودن محصول";
        return view('admin.depots.add_product',compact('title','depot_id'));
    }
}
