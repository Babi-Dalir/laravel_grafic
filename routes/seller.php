<?php
use App\Http\Controllers\Seller\SellerController;
use Illuminate\Support\Facades\Route;

//Seller Request Route
Route::get('seller_requests', [SellerController::class, 'sellerRequestsList'])->name('seller.requests.list');

Route::get('seller_requests/{sellerRequest}', [SellerController::class, 'detailSellerRequest'])->name('seller.requests.detail');

Route::post('seller_requests/{sellerRequest}/approve', [SellerController::class, 'approveSellerRequest'])->name('seller.requests.approve');

Route::post('seller_requests/{sellerRequest}/reject', [SellerController::class, 'rejectSellerRequest'])->name('seller.requests.reject');

//Seller Product Route
Route::get('seller_product_list',[SellerController::class,'sellerProductList'])->name('seller.product.list');

Route::get('create_seller_product',[SellerController::class,'createSellerProduct'])->name('create.seller.product');
Route::post('store_seller_product',[SellerController::class,'storeSellerProduct'])->name('store.seller.product');

Route::get('seller_transaction_list',[SellerController::class,'sellerTransactionList'])->name('seller.transaction.list');
