<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\CartsController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('products', [ProductsController::class, 'store']);
Route::get('products', [ProductsController::class, 'list']);
Route::get('products/{id}', [ProductsController::class, 'detail']);

Route::get('order', [OrdersController::class, 'list']);
Route::post('order/checkout', [OrdersController::class, 'checkout']);
Route::get('order/summary/{id}', [OrdersController::class, 'detail']);


Route::post('carts', [CartsController::class, 'create']);
Route::get('carts', [CartsController::class, 'list']);