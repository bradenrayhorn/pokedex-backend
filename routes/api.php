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

Route::get('pokemon/{id}', 'PokedexController@details');

Route::get('pokemon', 'PokedexController@paginatedPokemon');

Route::group(['middleware' => 'checkapitoken'], function () {
    Route::get('user', function(){
        return response()->json(Auth::guard('api')->user(), 200);
    });

    Route::post('pokemon/capture', 'PokedexController@capture');

    Route::post('pokemon/captured', 'PokedexController@listCaptured');
});

Route::post('user/register', 'UserController@register');

Route::post('user/token', 'UserController@getToken');
