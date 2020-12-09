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
    Route::get('category', 'CategoryController@index')->name('category');
    Route::group(['prefix' => 'category', 'as'=>'category.'], function () {
        Route::post('datatable-data', 'CategoryController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'CategoryController@store_or_update_data')->name('store.or.update');
        Route::post('edit', 'CategoryController@edit')->name('edit');
        Route::post('delete', 'CategoryController@delete')->name('delete');
        Route::post('bulk-delete', 'CategoryController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'CategoryController@change_status')->name('change.status');
    });
});
