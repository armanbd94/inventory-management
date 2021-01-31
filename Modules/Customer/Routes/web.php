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
    Route::get('customer', 'CustomerController@index')->name('customer');
    Route::group(['prefix' => 'customer', 'as'=>'customer.'], function () {
        Route::post('datatable-data', 'CustomerController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'CustomerController@store_or_update_data')->name('store.or.update');
        Route::post('show', 'CustomerController@show')->name('show');
        Route::post('edit', 'CustomerController@edit')->name('edit');
        Route::post('delete', 'CustomerController@delete')->name('delete');
        Route::post('bulk-delete', 'CustomerController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'CustomerController@change_status')->name('change.status');
        Route::get('group-data/{id}','CustomerController@groupData');
    });
});
