<?php

use App\Http\Controllers\FrontEnd\HomeController;
use App\Http\Controllers\FrontEnd\PaymentController;
use App\Http\Controllers\FrontEnd\ProductController;
use App\Http\Controllers\FrontEnd\ProfileController;
use App\Http\Controllers\FrontEnd\SearchController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';



Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/single_products/{slug}', [ProductController::class, 'singleProduct'])->name('single.product');

Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

Route::get('/main_category_product_list/{main_slug}', [ProductController::class, 'mainCategoryProductList'])->name('main.category.product.list');
Route::get('/search_category_product_list/{sub_slug}/{child_slug?}', [ProductController::class, 'searchCategoryProductList'])->name('search.category.product.list');

Route::get('/compare_products/{product_id_1}/{product_id_2}', [ProductController::class, 'compareProducts'])->name('compare.products');

// search ajax in header
Route::get('/ajax-search', [SearchController::class, 'ajaxSearch'])->name('ajax.search');

Route::middleware('auth')->group(function () {

    Route::get('/user_cart', [HomeController::class, 'userCart'])->name('user.cart');

    Route::get('/shopping', [HomeController::class, 'shopping'])->name('user.shopping');

    Route::get('/shopping_payment', [HomeController::class, 'shoppingPayment'])->name('user.shopping.payment');

    Route::get('/payment', [PaymentController::class, 'payment'])->name('payment');

    Route::get('/product_comment/{product_id}', [ProductController::class, 'productComment'])->name('product.comment');

    //Profile
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile_update', [ProfileController::class, 'profileUpdate'])->name('profile.update');
    Route::get('/profile_orders', [ProfileController::class, 'profileOrders'])->name('profile.orders');
    Route::get('/profile_order_details/{order_id}', [ProfileController::class, 'profileOrdersDetails'])->name('profile.order.details');
    Route::get('/profile_favorites', [ProfileController::class, 'profileFavorites'])->name('profile.favorites');
    Route::get('/profile_comments', [ProfileController::class, 'profileComments'])->name('profile.comments');
    Route::get('/profile_addresses', [ProfileController::class, 'profileAddresses'])->name('profile.addresses');
    Route::get('/profile_seller', [ProfileController::class, 'profileSeller'])->name('profile.seller');
    Route::post('/profile_store_seller', [ProfileController::class, 'profileStoreSeller'])->name('profile.store.seller');
});
