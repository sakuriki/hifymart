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
    Route::apiResource('products', 'Admin\ProductController');
    Route::apiResource('categories', 'Admin\CategoryController');
  });
  Route::get('/auth/user', 'Auth\AuthController@user');
});
