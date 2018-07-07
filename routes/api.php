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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//reccords routes
Route::middleware('auth:api')->resource('/record','RecordsController',['except' => ['create','store','edit']]);
Route::middleware('auth:api')->post('/record/create','RecordsController@create');
Route::middleware('auth:api')->get('/record/history/{record}','RecordsController@viewHistory');
Route::middleware('auth:api')->post('/record/freeze/{record}','RecordsController@freeze');
Route::middleware('auth:api')->post('/record/unfreeze/{record}','RecordsController@unfreeze');
Route::middleware('auth:api')->get('/record/op-count/all','RecordsController@totalOperationsCount');
Route::middleware('auth:api')->get('/record/op-count/{record}','RecordsController@operationsCount');
Route::middleware('auth:api')->get('/record/rollback/{record}','RecordsController@rollback');
Route::middleware('auth:api')->get('/record/fork/{record}','RecordsController@fork');


//user routes
Route::post('login','PassportController@login');
Route::post('register','PassportController@register');
Route::middleware('auth:api')->post('get-details','PassportController@getDetails');
