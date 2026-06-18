<?php

use App\Http\Controllers\Admin\ProductFileController;
use App\Http\Controllers\Seller\SellerController;
use Illuminate\Support\Facades\Route;

//Seller Request Route
Route::get('seller_requests', [SellerController::class, 'sellerRequestsList'])
    ->name('seller.requests.list');


//Seller Product Route
Route::get('seller_product_list',[SellerController::class,'sellerProductList'])->name('seller.product.list');

Route::get('create_seller_product',[SellerController::class,'createSellerProduct'])->name('create.seller.product');
Route::post('store_seller_product',[SellerController::class,'storeSellerProduct'])->name('store.seller.product');

Route::get('edit_seller_product/{id}', [SellerController::class, 'editSellerProduct'])->name('edit.seller.product');
Route::put('update_seller_product/{id}', [SellerController::class, 'updateSellerProduct'])->name('update.seller.product');

// Gallery Route
Route::get('add_seller_product_galleries/{id}', [SellerController::class, 'addSellerProductGallery'])->name('add.seller.product.gallery');
Route::post('store_seller_product_galleries/{id}', [SellerController::class, 'storeSellerProductGallery'])->name('store.seller.product.gallery');

//Property Route
Route::get('create_seller_product_properties/{product}', [SellerController::class, 'createSellerProductProperty'])->name('create.seller.product.properties');

//Product File Route
Route::get('seller_products/{product}/files',[ProductFileController::class,'index'])->name('seller.product.file.list');


//Seller Transaction Route
Route::get('seller_transaction_list',[SellerController::class,'sellerTransactionList'])->name('seller.transaction.list');

//Seller verification Route
Route::get('create_seller_verification',[SellerController::class,'createSellerVerification'])->name('create.seller.verification');
Route::post('store_seller_verification',[SellerController::class,'storeSellerVerification'])->name('store.seller.verification');
