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

Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1'], function(){
    //Multipart
    Route::post('multipart', [
        'as' => 'api.multipart',
        'uses' => 'MultipartController@postMedia'
    ]);

    Route::post('upload_multipart', [
        'as' => 'api.upload_multipart',
        'uses' => 'MultipartController@uploadMedia'
    ]);

    Route::post('upload_multipart1', [
        'as' => 'api.upload_multipart1',
        'uses' => 'MultipartController@uploadMedia1'
    ]);
});
