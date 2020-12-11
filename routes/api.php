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

Route::group(['prefix' => '/auth', 'middleware' => 'api'], function () {
  Route::post('/register', 'Auth\AuthController@register')->name('register')->middleware('auth:api');
  Route::post('/login', 'Auth\AuthController@login')->name('login');
});
Route::group(['prefix' => '/admin', 'middleware' => 'jwtnew'], function () {
  Route::apiResource('products', 'Admin\ProductController');
  Route::get("/me", 'Auth\AuthController@user');
});
