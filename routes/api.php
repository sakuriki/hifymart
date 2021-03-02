<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/export', 'Admin\OrderController@export');
Route::get('/user/{id}', 'UserController@show');
Route::get('/provinces', 'ProvinceController@index');
Route::get('/coupons/{code}', 'CouponController');
Route::get('/footer', 'FooterController');
Route::get('/settings', 'SettingController');
Route::apiResource('/tags', 'TagController')->only(["index", "show"]);
Route::apiResource('brands', 'BrandController')->only(["index", "show"]);
Route::apiResource('taxes', 'TaxController')->only(["index", "show"]);
Route::apiResource('orders', 'OrderController');
Route::apiResource('categories', 'CategoryController')->only(["index", "show"]);
Route::apiResource('ratings', 'RatingController')->only(["show"]);
Route::apiResource('subscribes', 'SubscribeController')->only(["store", "destroy"]);
Route::group(['prefix' => '/auth', ['middleware' => 'throttle:20,5']], function () {
  Route::post('/register', 'Auth\AuthController@register')->name('register');
  Route::post('/login', 'Auth\AuthController@login')->name('login');
});
Route::group(['middleware' => 'jwtnew'], function () {
  Route::group(['prefix' => '/admin'], function () {
    Route::get('slides', 'Admin\SlideController@index');
    Route::post('slides', 'Admin\SlideController@store');
    Route::patch('slides', 'Admin\SlideController@update');
    Route::delete('slides', 'Admin\SlideController@destroy');
    Route::patch('settings', 'Admin\SettingController');
    Route::delete('tags/bulkDestroy', 'Admin\TagController@bulkDestroy');
    Route::delete('taxes/bulkDestroy', 'Admin\TaxController@bulkDestroy');
    Route::delete('roles/bulkDestroy', 'Admin\RoleController@bulkDestroy');
    Route::delete('users/bulkDestroy', 'Admin\UserController@bulkDestroy');
    Route::delete('brands/bulkDestroy', 'Admin\BrandController@bulkDestroy');
    Route::delete('orders/bulkDestroy', 'Admin\OrderController@bulkDestroy');
    Route::delete('coupons/bulkDestroy', 'Admin\CouponController@bulkDestroy');
    Route::delete('ratings/bulkDestroy', 'Admin\RatingController@bulkDestroy');
    Route::delete('comments/bulkDestroy', 'Admin\CommentController@bulkDestroy');
    Route::delete('products/bulkDestroy', 'Admin\ProductController@bulkDestroy');
    Route::delete('categories/bulkDestroy', 'Admin\CategoryController@bulkDestroy');
    Route::delete('permissions/bulkDestroy', 'Admin\PermissionController@bulkDestroy');
    Route::delete('productImages/bulkDestroy', 'Admin\ProductImageController@bulkDestroy');
    Route::apiResource('tags', 'Admin\TagController');
    Route::apiResource('taxes', 'Admin\TaxController');
    Route::apiResource('roles', 'Admin\RoleController');
    Route::apiResource('users', 'Admin\UserController');
    Route::apiResource('brands', 'Admin\BrandController');
    Route::apiResource('orders', 'Admin\OrderController');
    Route::apiResource('coupons', 'Admin\CouponController');
    Route::apiResource('ratings', 'Admin\RatingController');
    Route::apiResource('comments', 'Admin\CommentController');
    Route::apiResource('products', 'Admin\ProductController');
    Route::apiResource('categories', 'Admin\CategoryController');
    Route::apiResource('permissions', 'Admin\PermissionController');
    Route::apiResource('productImages', 'Admin\ProductImageController');
    Route::get('/dashboard', 'Admin\DashboardController');
  });
  Route::get('/auth/user', 'Auth\AuthController@user');
  Route::apiResource('wishlists', 'WishlistController')->only(["index", "store", "destroy"]);
  Route::apiResource('addressBooks', 'AddressBookController');
});
Route::group(['middleware' => 'jwtoptinal'], function () {
  Route::post('/checkout', 'CheckoutController@store');
  Route::get('/payment/vnpay', 'CheckoutController@test')->name('vnpay.index');
  Route::get('/payment/vnpay/process', 'CheckoutController@returnVnpay')->name('vnpay.process');
  // Route::get('/payment/vnpay/ipn', 'CheckoutController@test2')->name('vnpay.ipn');
  Route::get('productlist', 'ProductController@showById');
  Route::apiResource('products', 'ProductController')->only(["index", "show"]);
  Route::get('/comments/showreply/{id}', 'ProductCommentController@showreply');
  Route::apiResource('comments', 'ProductCommentController');
});
