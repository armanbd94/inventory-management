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
    Route::get('supplier', 'SupplierController@index')->name('supplier');
    Route::group(['prefix' => 'supplier', 'as'=>'supplier.'], function () {
        Route::post('datatable-data', 'SupplierController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'SupplierController@store_or_update_data')->name('store.or.update');
        Route::post('show', 'SupplierController@show')->name('show');
        Route::post('edit', 'SupplierController@edit')->name('edit');
        Route::post('delete', 'SupplierController@delete')->name('delete');
        Route::post('bulk-delete', 'SupplierController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'SupplierController@change_status')->name('change.status');
    });
});
