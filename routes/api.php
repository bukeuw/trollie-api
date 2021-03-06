<?php

use Illuminate\Http\Request;

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

Route::middleware(['api'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::get('user', 'AuthController@user');
    });

    Route::apiResource('boards', 'BoardController')->only([
        'index',
        'show',
        'store',
        'update',
    ]);

    Route::apiResource('lists', 'ListController')->only([
        'index',
        'show',
        'store',
        'update',
    ]);

    Route::apiResource('cards', 'CardController')->only([
        'index',
        'show',
        'store',
        'update',
    ]);

    Route::post('cards/{id}/due-date', 'CardController@addDueDate');
    Route::delete('cards/{id}/due-date', 'CardController@removeDueDate');

    Route::apiResource('statuses', 'StatusController')->only([
        'index',
        'show',
        'store',
        'update',
    ]);
    Route::post('statuses/toggle', 'StatusController@toggleStatus');

    Route::get('users', 'UserController@index');
    Route::post('cards/{id}/membership', 'CardController@toggleMembership');
    Route::get('users/{id}/notifications', 'UserController@getNotifications');
});
