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
    Route::get('sale', 'SaleController@index')->name('sale');
    Route::group(['prefix' => 'sale', 'as'=>'sale.'], function () {
        Route::post('datatable-data', 'SaleController@get_datatable_data')->name('datatable.data');
        Route::post('store', 'SaleController@store')->name('store');
        Route::post('update', 'SaleController@update')->name('update');
        Route::get('add', 'SaleController@create')->name('add');
        Route::get('details/{id}', 'SaleController@show')->name('show');
        Route::get('edit/{id}', 'SaleController@edit')->name('edit');
        Route::post('delete', 'SaleController@delete')->name('delete');
        Route::post('bulk-delete', 'SaleController@bulk_delete')->name('bulk.delete');
    });

    //Sale Payment Routes
    Route::post('sale-payment-store-or-update', 'SalePaymentController@store_or_update')->name('sale.payment.store.or.update');
    Route::post('sale-payment/view', 'SalePaymentController@show')->name('sale.payment.show');
    Route::post('sale-payment/edit', 'SalePaymentController@edit')->name('sale.payment.edit');
    Route::post('sale-payment/delete', 'SalePaymentController@delete')->name('sale.payment.delete');
});
