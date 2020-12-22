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

Route::post('/user/login', function (Request $request) {
    return (new \App\Http\Controllers\LoginController())->authenticate($request);
});
Route::post('/user/register', function (Request $request) {
    return (new \App\Http\Controllers\LoginController())->register($request);
});

Route::middleware('auth')->get('/user', function (Request $request) {
    return Auth::user();
});

Route::group(['prefix' => 'customer', 'middleware' => ['auth']], function () {
    App\Http\Controllers\CustomerController::routes();
});

Route::group(['prefix' => 'customer/numbers', 'middleware' => ['auth']], function () {
    App\Http\Controllers\NumberController::routes();
});