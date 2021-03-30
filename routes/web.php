<?php

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
    echo "Line Webhook";
});

$router->post('/', "LineController@getChat");

$router->get('/list_hastag', "LineController@listHastag");
$router->get('/analytics_by_hastag', "LineController@getAnalitycsByHastag");
