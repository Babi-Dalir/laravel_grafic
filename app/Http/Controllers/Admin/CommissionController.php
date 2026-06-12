<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommissionRequest;
use App\Models\Category;
use App\Models\Commission;

class CommissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "لیست کمیسیون ها";
        return view('admin.commissions.list', compact('title'));
    }

    /**\
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "ایجاد کمیسیون";
        $categories = Category::getLeafCategories();
        return view('admin.commissions.create',compact('title','categories'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CommissionRequest $request)
    {
        Commission::createCommission($request);
        return redirect()->route('commissions.index')->with('message', 'کمیسیون با موفقیت ایجاد شد');
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
        $title ="ویرایش کمیسیون";
        $categories = Category::getLeafCategories();
        $commission = Commission::query()->findOrfail($id);
        return view('admin.commissions.edit',compact('title','categories','commission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommissionRequest $request, string $id)
    {
        Commission::updateCommission($request,$id);
        return redirect()->route('commissions.index')->with('message', 'کمیسیون با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
