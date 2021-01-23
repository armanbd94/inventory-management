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
    Route::get('expense', 'ExpenseController@index')->name('expense');
    Route::group(['prefix' => 'expense', 'as'=>'expense.'], function () {
        Route::post('datatable-data', 'ExpenseController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'ExpenseController@store_or_update_data')->name('store.or.update');
        Route::post('edit', 'ExpenseController@edit')->name('edit');
        Route::post('delete', 'ExpenseController@delete')->name('delete');
        Route::post('bulk-delete', 'ExpenseController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'ExpenseController@change_status')->name('change.status');

        //Expense Category Routes
        Route::get('category', 'ExpenseCategoryController@index')->name('category');
        Route::group(['prefix' => 'category', 'as'=>'category.'], function () {
            Route::post('datatable-data', 'ExpenseCategoryController@get_datatable_data')->name('datatable.data');
            Route::post('store-or-update', 'ExpenseCategoryController@store_or_update_data')->name('store.or.update');
            Route::post('edit', 'ExpenseCategoryController@edit')->name('edit');
            Route::post('delete', 'ExpenseCategoryController@delete')->name('delete');
            Route::post('bulk-delete', 'ExpenseCategoryController@bulk_delete')->name('bulk.delete');
            Route::post('change-status', 'ExpenseCategoryController@change_status')->name('change.status');
        });
    });
});
