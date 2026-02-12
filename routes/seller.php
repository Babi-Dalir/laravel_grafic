<?php
use App\Http\Controllers\Seller\SellerController;
use Illuminate\Support\Facades\Route;


Route::get('seller_product_list',[SellerController::class,'sellerProductList'])->name('seller.product.list');

Route::get('create_seller_product',[SellerController::class,'createSellerProduct'])->name('create.seller.product');
Route::post('store_seller_product',[SellerController::class,'storeSellerProduct'])->name('store.seller.product');

Route::get('seller_transaction_list',[SellerController::class,'sellerTransactionList'])->name('seller.transaction.list');
