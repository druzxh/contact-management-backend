<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Test;

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

Route::group(['prefix' => 'v1'], function () {
    Route::get('/success', [Test::class, 'successExample']);
    Route::post('/success/created', [Test::class, 'successCreatedExample']);

    Route::post('/error', [Test::class, 'errorExample']);
    Route::get('/404', [Test::class, 'notFoundExample']);

    Route::get('/unauthorized', [Test::class, 'unauthorizedExample']);
    Route::get('/unauthenticated', [Test::class, 'unauthenticatedExample']);

    Route::get('/forbidden', [Test::class, 'forbiddenExample']);
    Route::get('/error/internal', [Test::class, 'serverErrorExample']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});