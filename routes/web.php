<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/{anyRoutes}', function () {
    if(file_exists(public_path() . '/index.html')) {
        return response()->file(public_path() . '/index.html');
    } else {
        return view('welcome');
    }
})->where('anyRoutes', '^((?!(api)).)*$');

Route::get('/{ng_route}', function () {
    return response()->file( public_path() . '/index.html' );
});
Route::get('/{ng_route}/{ng_sub_route}', function () {
    return response()->file( public_path() . '/index.html' );
});
Route::get('/{ng_route}/{ng_sub_route}/{ng_sub_sub_route}', function () {
    return response()->file( public_path() . '/index.html' );
});
Route::get('/{ng_route}/{ng_sub_route}/{ng_sub_sub_route}/{ng_sub_sub_sub_route}', function () {
    return response()->file( public_path() . '/index.html' );
});
Route::get('/{ng_route}/{ng_sub_route}/{ng_sub_sub_route}/{ng_sub_sub_sub_route}/{ng_sub_sub_sub_sub_route}', function () {
    return response()->file( public_path() . '/index.html' );
});
Route::get('/{ng_route}/{ng_sub_route}/{ng_sub_sub_route}/{ng_sub_sub_sub_route}/{ng_sub_sub_sub_sub_route/{ng_sub_sub_sub_sub_sub_route}', function () {
    return response()->file( public_path() . '/index.html' );
});
