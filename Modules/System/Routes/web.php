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
    Route::get('brand', 'BrandController@index')->name('brand');
    Route::group(['prefix' => 'brand', 'as'=>'brand.'], function () {
        Route::post('datatable-data', 'BrandController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'BrandController@store_or_update_data')->name('store.or.update');
        Route::post('edit', 'BrandController@edit')->name('edit');
        Route::post('delete', 'BrandController@delete')->name('delete');
        Route::post('bulk-delete', 'BrandController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'BrandController@change_status')->name('change.status');
    });

    //Tax Routes
    Route::get('tax', 'TaxController@index')->name('tax');
    Route::group(['prefix' => 'tax', 'as'=>'tax.'], function () {
        Route::post('datatable-data', 'TaxController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'TaxController@store_or_update_data')->name('store.or.update');
        Route::post('edit', 'TaxController@edit')->name('edit');
        Route::post('delete', 'TaxController@delete')->name('delete');
        Route::post('bulk-delete', 'TaxController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'TaxController@change_status')->name('change.status');
    });
});
