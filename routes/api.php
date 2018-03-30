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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/



/** Available Routes */

Route::group([

    'middleware' => ['api','cors'],
    'prefix' => 'v1'

], function () {

    /** Authentication Routes */

    Route::group([

        'prefix' => 'auth'

    ],function() {

        Route::post('/login', 'API\V1\AuthController@login');
        Route::post('/register', 'API\V1\AuthController@register');
        Route::post('/forget_password', 'API\V1\AuthController@forget_password');
        Route::post('/reset_password', 'API\V1\AuthController@reset_password');
        Route::post('/logout', 'API\V1\AuthController@logout');

    });


    /** Authentication Routes */

    Route::group([
        'middleware'    =>  'jwt.auth'
    ],function() {

        /** Authentication Routes */

        Route::group([

            'prefix' => 'auth'

        ],function() {

            

        });

    });


});

