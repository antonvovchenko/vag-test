<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//uncomment to show all DB queries
//Event::listen('illuminate.query', function($query)
//{
//    var_dump($query);
//});


Blade::setContentTags('<%', '%>');
Blade::setEscapedContentTags('<%%', '%%>');

//index page and test headers
Route::get('/', 'WelcomeController@index');
Route::post('/', 'WelcomeController@index');
Route::get('test', 'WelcomeController@headers');

//print smth
Route::get('print-screen', 'PrintController@printScreen');
//reports
Route::controller('report', 'ReportController');

//api
Route::group([
    'prefix' => 'api',
    'namespace' => 'Api'
], function () {

    //auth
    Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');

    //users rest crud and others
    Route::controller('user-helper', 'UserController');
    Route::resource('user', 'UserController');

    //products rest crud and others
    Route::controller('product-helper', 'ProductController');
    Route::resource('product', 'ProductController');

    //product-markup-type rest crud and others
    Route::controller('product-markup-type-helper', 'ProductMarkupTypeController');
    Route::resource('product-markup-type', 'ProductMarkupTypeController');

    //supplier rest crud and others
    Route::controller('supplier-helper', 'SupplierController');
    Route::resource('supplier', 'SupplierController');

    //inventory rest crud and others
    Route::controller('inventory-helper', 'InventoryController');
    Route::resource('inventory', 'InventoryController');

    //patient rest crud and others
    Route::controller('patient-helper', 'PatientController');
    Route::resource('patient', 'PatientController');

    //prescriber rest crud and others
    Route::controller('prescriber-helper', 'PrescriberController');
    Route::resource('prescriber', 'PrescriberController');

    //prescription rest crud and others
    Route::controller('prescription-helper', 'PrescriptionController');
    Route::resource('prescription', 'PrescriptionController');

    //prescription_item rest crud
    Route::resource('prescription_item', 'PrescriptionItemController');

    //label code rest crud and others
    Route::controller('label-code-helper', 'LabelCodeController');
    Route::resource('label-code', 'LabelCodeController');

    //tax rest crud and others
    Route::controller('tax-helper', 'TaxController');
    Route::resource('tax', 'TaxController');

    //log rest crud
    Route::resource('log', 'LogController');

    //order rest crud and others
    Route::controller('order-helper', 'OrderController');
    Route::resource('order', 'OrderController');

    //transaction requests
    Route::controller('transaction', 'TransactionController');
    Route::resource('transaction-request', 'TransactionRequestController');

    //fee rest crud and others
    Route::controller('fee-helper', 'FeeController');
    Route::resource('fee', 'FeeController');

    //import
    Route::controller('import-data', 'ImportDataController');

    //settings
    Route::controller('settings-helper', 'SettingsController');
    Route::resource('settings', 'SettingsController');

    //customer
    Route::controller('customer-helper', 'CustomerController');
    Route::resource('customer', 'CustomerController');

});