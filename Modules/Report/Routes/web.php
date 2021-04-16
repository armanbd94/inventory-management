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
    Route::get('summary-report', 'SummaryReportController@index')->name('summary.report');
    Route::post('summary-report/details', 'SummaryReportController@report')->name('summary.report.details');
    
    Route::match(['get', 'post'], 'product-report', 'ProductReportController@index');

    //daily sale routes
    Route::get('daily-sale','SaleReportController@dailySale');
    Route::post('daily-sale-report','SaleReportController@dailySaleReport')->name('daily.sale.report');

    //monthly sale routes
    Route::get('monthly-sale','SaleReportController@monthlySale');
    Route::post('monthly-sale-report','SaleReportController@monthlySaleReport')->name('monthly.sale.report');

    //daily purchase routes
    Route::get('daily-purchase','PurchaseReportController@dailyPurchase');
    Route::post('daily-purchase-report','PurchaseReportController@dailyPurchaseReport')->name('daily.purchase.report');

    //monthly purchase routes
    Route::get('monthly-purchase','PurchaseReportController@monthlyPurchase');
    Route::post('monthly-purchase-report','PurchaseReportController@monthlyPurchaseReport')->name('monthly.purchase.report');
    
    //customer report routes
    Route::get('customer-report','CustomerReportController@index');
    Route::post('customer-report/datatable-data','CustomerReportController@get_datatable_data')->name('customer.report.datatable.data');
    
    //supplier report routes
    Route::get('supplier-report','SupplierReportController@index');
    Route::post('supplier-report/datatable-data','SupplierReportController@get_datatable_data')->name('supplier.report.datatable.data');
    
    //Product quantity alert routes
    Route::get('product-quantity-alert','ProductQuantityAlertController@index');
    Route::post('product-quantity-alert/datatable-data','ProductQuantityAlertController@get_datatable_data')->name('product.quantity.alert.datatable.data');
});
