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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum']);

Route::post('/auth/login', function (Request $request) {
    return response()->json(\App\Models\User::all());
});


Route::get('/home', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum']);


//The abilities middleware may be assigned to a route to verify that
// the incoming request's token has all of the listed abilities:
Route::get('/orders', function () {
    // Token has both "check-status" and "place-orders" abilities...
})->middleware(['auth:sanctum', 'abilities:check-status,place-orders']);

//The ability middleware may be assigned to a route to verify that
// the incoming request's token has at least one of the listed abilities:
Route::get('/orders', function () {
    // Token has the "check-status" or "place-orders" ability...
})->middleware(['auth:sanctum', 'ability:check-status,place-orders']);
