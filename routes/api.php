<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apiOfficesController as apiGoodsController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('/newOffice', 'App\Http\Controllers\apiOfficesController@newOffice');
    Route::post('/updateOffice', 'App\Http\Controllers\apiOfficesController@updateOffice');
    Route::post('/deleteOffice', 'App\Http\Controllers\apiOfficesController@deleteOffice');
    Route::get('/offices', 'App\Http\Controllers\apiOfficesController@getAllOffices');


