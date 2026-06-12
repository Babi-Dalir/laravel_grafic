<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSellerController extends Controller
{
    public function downloadResume(SellerRequest $request)
    {
        $path = Storage::disk('files')->path($request->resume);
        return response()->download($path);
    }

    public function sellerList()
    {
        $title = "لیست فروشندگان";
        return view('admin.sellers.list',compact('title'));
    }

    public function sellerSettlementList()
    {
        $title = "لیست تسویه حساب ها";
        return view('seller.seller_settlements.list', compact('title'));
    }


    public function adminSellerTransactionList()
    {
        $title = 'لیست تراکنش فروشندگان';

        return view('admin.transactions.list', compact('title'));
    }
}
