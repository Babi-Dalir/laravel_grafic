<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountCampaignRequest;
use App\Models\Category;
use App\Models\DiscountCampaign;
use App\Models\Product;

class DiscountCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "لیست کمپین ها";
        return view('admin.discount_campaigns.list', compact('title'));
    }

    /**\
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "ایجاد کمپین";
        $categories = Category::getCategories();
        $products = Product::query()->pluck('name','id');
        return view('admin.discount_campaigns.create',compact('title','categories','products'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(DiscountCampaignRequest $request)
    {
        DiscountCampaign::createCampaign($request);
        return redirect()->route('discount_campaigns.index')->with('message', 'کمپین جدید با موفقیت ایجاد شد');
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
        $title ="ویرایش کمپین";
        $categories = Category::getCategories();
        $products = Product::query()->pluck('name','id');
        $discount_campaign = DiscountCampaign::findOrfail($id);
        // این متغیر حیاتیه برای اینکه در ویو بفهمیم کدوم محصولا قبلا انتخاب شدن
        $selectedTargets = $discount_campaign->targets()->pluck('target_id')->toArray();
        return view('admin.discount_campaigns.edit',compact('title','categories','products','discount_campaign','selectedTargets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DiscountCampaignRequest $request, string $id)
    {
        DiscountCampaign::updateCampaign($request,$id);
        return redirect()->route('discount_campaigns.index')->with('message', 'کمپین با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
