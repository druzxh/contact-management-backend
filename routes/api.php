<?php

use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Test;
use App\Http\Controllers\Api\AuthController;

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

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/token', [AuthController::class, 'checkToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'contact'], function () {
        Route::get('all', [ContactController::class, 'allContact']);
        Route::get('detail/{contact_code}', [ContactController::class, 'detailContact']);
        Route::post('add', [ContactController::class, 'addContact']);
        Route::post('update', [ContactController::class, 'updateContact']);
        Route::post('delete', [ContactController::class, 'deleteContact']);
    });

    Route::group(['prefix' => 'group/contact'], function () {
        Route::get('all', [GroupController::class, 'allContactGroup']);
        Route::get('detail/{contact_code}', [GroupController::class, 'detailContactGroup']);
        Route::post('add', [GroupController::class, 'addContactGroup']);
        Route::post('update', [GroupController::class, 'updateContactGroup']);
        Route::post('delete', [GroupController::class, 'deleteContactGroup']);
    });
    Route::group(['prefix' => 'group'], function () {
        Route::post('insert/contact', [GroupController::class, 'insertContactToGroup']);
        Route::post('remove/contact', [GroupController::class, 'removeContactFromGroup']);
        ;
    });
});