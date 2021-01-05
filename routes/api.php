<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => '/auth', ['middleware' => 'throttle:20,5']], function () {
  Route::post('/register', 'Auth\AuthController@register')->name('register');
  Route::post('/login', 'Auth\AuthController@login')->name('login');
});
Route::group(['middleware' => 'jwtnew'], function () {
  Route::group(['prefix' => '/admin'], function () {
    Route::apiResource('roles', 'Admin\RoleController');
    Route::apiResource('orders', 'Admin\OrderController');
    Route::apiResource('products', 'Admin\ProductController');
    Route::apiResource('categories', 'Admin\CategoryController');
    Route::apiResource('permissions', 'Admin\PermissionController');
  });
  Route::get('/auth/user', 'Auth\AuthController@user');
  Route::apiResource('wishlists', 'WishlistController')->only(["index", "show"]);
});
Route::group(['middleware' => 'jwtoptinal'], function () {
  Route::post('/checkout', 'CheckoutController@store');
  Route::get('/payment/vnpay', 'CheckoutController@test')->name('vnpay.index');
  Route::get('/payment/vnpay/process', 'CheckoutController@returnVnpay')->name('vnpay.process');
  // Route::get('/payment/vnpay/ipn', 'CheckoutController@test2')->name('vnpay.ipn');
  Route::get('productlist', 'ProductController@showById');
  Route::apiResource('products', 'ProductController')->only(["index", "show"]);
});
Route::get('/user/{id}', 'UserController@show');
Route::get('/provinces', 'ProvinceController@index');
Route::apiResource('brands', 'BrandController')->only(["index", "show"]);
Route::apiResource('orders', 'OrderController');
Route::apiResource('categories', 'CategoryController')->only(["index", "show"]);
Route::apiResource('ratings', 'RatingController')->only(["show"]);
