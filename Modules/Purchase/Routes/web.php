<?php

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
    Route::get('purchase', 'PurchaseController@index')->name('purchase');
    Route::group(['prefix' => 'purchase', 'as'=>'purchase.'], function () {
        Route::post('datatable-data', 'PurchaseController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'PurchaseController@store_or_update_data')->name('store.or.update');
        Route::get('add', 'PurchaseController@create')->name('add');
        Route::get('show', 'PurchaseController@show')->name('show');
        Route::get('edit', 'PurchaseController@edit')->name('edit');
        Route::post('delete', 'PurchaseController@delete')->name('delete');
        Route::post('bulk-delete', 'PurchaseController@bulk_delete')->name('bulk.delete');
        Route::get('invoice', 'PurchaseController@invoice')->name('invoice');
        Route::post('payment/add', 'PurchasePaymentController@add')->name('payment.add');
        Route::post('payment/edit', 'PurchasePaymentController@edit')->name('payment.edit');
        Route::post('payment/view', 'PurchasePaymentController@view')->name('payment.view');
        Route::post('payment/delete', 'PurchasePaymentController@delete')->name('payment.delete');
    });
});
