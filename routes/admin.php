<?php

use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\DepotController;
use App\Http\Controllers\Admin\DiscountCampaignController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GiftCartController;
use App\Http\Controllers\Admin\GuarantyController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductFileController;
use App\Http\Controllers\Admin\ProductPriceController;
use App\Http\Controllers\Admin\PropertyGroupController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\SellerController;
use Illuminate\Support\Facades\Route;


//Main Route
Route::get('/', [PanelController::class, 'index'])->name('panel');

//Users Route
Route::resource('users', UserController::class);
Route::get('create_user_role/{id}', [UserController::class, 'createUserRole'])->name('create.user.role');
Route::post('store_user_role/{id}', [UserController::class, 'storeUserRole'])->name('store.user.role');

//roles Route
Route::resource('roles', RoleController::class);
Route::get('create_role_permission/{id}', [RoleController::class, 'createRolePermission'])->name('create.role.permission');
Route::post('store_role_permission/{id}', [RoleController::class, 'storeRolePermission'])->name('store.role.permission');

//permissions Route
Route::resource('permissions', PermissionController::class);

//categories Route
Route::resource('categories', CategoryController::class);
Route::get('categories_trashed', [CategoryController::class, "trashed"])->name('categories.trashed');

//tags Route
Route::resource('tags', TagController::class);

//products Route
Route::resource('products', ProductController::class);
Route::get('products_trashed', [ProductController::class, "trashed"])->name('products.trashed');

// Gallery Route
Route::get('add_product_galleries/{id}', [ProductController::class, 'addGallery'])->name('add.product.gallery');

Route::post('store_product_galleries/{id}', [ProductController::class, 'storeGallery'])->name('store.product.gallery');

//PropertyGroup Route
Route::resource('property_groups', PropertyGroupController::class);

//Property Route
Route::get('create_product_properties/{product}', [ProductController::class, 'createProductProperty'])->name('create.product.properties');

//Slider Route
Route::resource('sliders', SliderController::class);

//Banner Route
Route::resource('banners', BannerController::class);

//Comment Route
Route::get('users_comments', [CommentController::class, 'userComments'])->name('users.comments');

//Discount Route
Route::resource('discounts', DiscountController::class);

//GiftCart Route
Route::resource('gift_carts', GiftCartController::class);

//Ckeditor Route
Route::post('upload_image_ckeditor', [GalleryController::class, 'ckeditorImage'])->name('ckeditor.upload');

//Order Route
//Route::group(['middleware' => ['can:لیست سفارشات']], function () {
Route::get('order_list', [OrderController::class, 'orders'])->name('admin.orders.list');
//});

Route::get('order_detail_list/{order}', [OrderController::class, 'orderDetails'])->name('admin.order.details.list');

//commissions Route
Route::resource('commissions', CommissionController::class);

//DiscountCampaign Route
Route::resource('discount_campaigns', DiscountCampaignController::class);

//Product File Route
Route::get('products/{product}/files',[ProductFileController::class,'index'])->name('product.file.list');

//Download Resume Route
Route::get('download_resume/{request}', [AdminSellerController::class, 'downloadResume'])
    ->name('download.resume');

//Seller List Route
Route::get('seller_list', [AdminSellerController::class, 'sellerList'])->name('seller.list');

//Seller Settlement Route
Route::get('seller_settlement_list',[AdminSellerController::class,'sellerSettlementList'])->name('seller.settlement.list');

//Seller Transaction Route
Route::get('seller_transaction',[AdminSellerController::class,'adminSellerTransactionList'])->name('admin.seller.transaction.list');

