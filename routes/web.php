<?php

use Illuminate\Support\Facades\Route;

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

use Illuminate\Routing\Router;
Auth::routes();
/** @var Router $router */

$router->get('/', 'ExpertController@index');

$router->group(['middleware'=>['auth']], function (Router $router) {
    $router->resource('appointments', 'BookingController')
        ->only(['store', 'destroy','index']);
    $router->get('book/{id}', 'BookingController@book')->name('expert.book');
    $router->post('book/{id}', 'BookingController@book')->name('book.time.slots');
});
