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

Route::group(['middleware' => ['auth']], function () {
    Route::get('stock', 'StockController@index')->name('stock');
    Route::group(['prefix' => 'stock', 'as'=>'stock.'], function () {
        Route::post('datatable-data', 'StockController@get_datatable_data')->name('datatable.data');
    });
});
