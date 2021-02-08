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
    Route::get('account', 'AccountController@index')->name('account');
    Route::group(['prefix' => 'account', 'as'=>'account.'], function () {
        Route::post('datatable-data', 'AccountController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'AccountController@store_or_update_data')->name('store.or.update');
        Route::post('edit', 'AccountController@edit')->name('edit');
        Route::post('delete', 'AccountController@delete')->name('delete');
        Route::post('bulk-delete', 'AccountController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'AccountController@change_status')->name('change.status');
    });

    Route::get('balance-sheet', 'AccountController@balance_Sheet')->name('balance.sheet');
});
