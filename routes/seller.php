<?php
use App\Http\Controllers\Seller\SellerController;
use Illuminate\Support\Facades\Route;

//Seller Request Route
Route::get('seller_requests', [SellerController::class, 'sellerRequestsList'])
    ->name('seller.requests.list');


//Seller Product Route
Route::get('seller_product_list',[SellerController::class,'sellerProductList'])->name('seller.product.list');

Route::get('create_seller_product',[SellerController::class,'createSellerProduct'])->name('create.seller.product');
Route::post('store_seller_product',[SellerController::class,'storeSellerProduct'])->name('store.seller.product');

//Seller Transaction Route
Route::get('seller_transaction_list',[SellerController::class,'sellerTransactionList'])->name('seller.transaction.list');

//Seller verification Route
Route::get('create_seller_verification',[SellerController::class,'createSellerVerification'])->name('create.seller.verification');
Route::post('store_seller_verification',[SellerController::class,'storeSellerVerification'])->name('store.seller.verification');
