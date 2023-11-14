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

Route::get("/",function (){
   return view('welcome');
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace' => "App\Http\Controllers\Api\\"

], function () {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('register', 'AuthController@register');
    Route::post('profile', 'AuthController@profile');
    Route::post('verfiyPhone', 'AuthController@verfiyPhone');

});

Route::group([
    'middleware' => ['jwt.verify', 'api'],
    'prefix' => 'user',
    'namespace' => "App\Http\Controllers\Api\\"
],function (){
    Route::post('getNearestUsers', 'UserController@getNearestUsers')->name("getNearestUsers");
    Route::post('updateToken', 'UserController@updateToken')->name("updateToken");
    Route::post('sendMessageToAll', 'UserController@sendMessageToAll')->name("sendMessageToAll");
});

