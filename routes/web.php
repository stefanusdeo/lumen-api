<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\InquiryVaController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('oauth/token', 'AuthController@login');
    $router->get('oauth/get-user-token', 'AuthController@getUserByToken');
    $router->post('inquiry-va', 'InquiryVaController@store');
    $router->post('payment-va', 'PaymentVaController@store');
});
