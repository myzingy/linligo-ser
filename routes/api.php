<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

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

Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});

$attributes = [
    //'prefix'        => config('api'),
    'namespace'     => config('App\\Http\\Controllers'),
    //'middleware'    => 'auth:api',
];
Route::group($attributes, function ($router) {

    //$router->get('/user', 'HomeController@index');
    /* @var \Illuminate\Routing\Router $router */
    $router->group(['middleware'    => 'auth:api'], function ($router) {

        /* @var \Illuminate\Routing\Router $router */
        $router->any('wxuser/setPhoneNumber', 'WxuserController@setPhoneNumber');

        $router->resource('activity', ActivityController::class);
        $router->resource('activity/myOrder', ActivityMyOrderController::class);

    });
    $router->post('wxuser/miniLogin', 'WxuserController@miniLogin');
    $router->get('itemName/{name}', 'ItemNameController@show');
});

