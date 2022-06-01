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
 
Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Auth::routes();

Route::get('/dashboard', 'DashboardController@index');
// Route::get('/system-management/{option}', 'SystemMgmtController@index');
Route::get('/profile', 'ProfileController@index');

Route::post('user-management/search', 'UserManagementController@search')->name('user-management.search');
Route::resource('user-management', 'UserManagementController');

Route::resource('employee-management', 'EmployeeManagementController');
Route::post('employee-management/search', 'EmployeeManagementController@search')->name('employee-management.search');

Route::resource('system-management/department', 'DepartmentController');
Route::post('system-management/department/search', 'DepartmentController@search')->name('department.search');

Route::resource('system-management/division', 'DivisionController');
Route::post('system-management/division/search', 'DivisionController@search')->name('division.search');

Route::resource('system-management/country', 'CountryController');
Route::post('system-management/country/search', 'CountryController@search')->name('country.search');

Route::resource('system-management/state', 'StateController');
Route::post('system-management/state/search', 'StateController@search')->name('state.search');

Route::resource('system-management/city', 'CityController');
Route::post('system-management/city/search', 'CityController@search')->name('city.search');

Route::get('system-management/report', 'ReportController@index');
Route::post('system-management/report/search', 'ReportController@search')->name('report.search');
Route::post('system-management/report/excel', 'ReportController@exportExcel')->name('report.excel');
Route::post('system-management/report/pdf', 'ReportController@exportPDF')->name('report.pdf');

Route::get('avatars/{name}', 'EmployeeManagementController@load');

/**
 * Transport companies CRUD
 */
Route::prefix('transport-companies')->group(function () {
    Route::get('index', 'CompaniesController@index')->name('transport.index');
    Route::get('create', 'CompaniesController@create')->name('transport.create');
    Route::post('save', 'CompaniesController@save')->name('transport.save');
    Route::get('edit/{id}', 'CompaniesController@edit')->name('transport.edit');
    Route::any('update/{id}', 'CompaniesController@update')->name('transport.update');
    Route::any('delete/{id}', 'CompaniesController@delete')->name('transport.delete');
});

/**
 * packages
 */
Route::prefix('packages')->group(function () {
    Route::any('index', 'PackagesController@index')->name('packages.index');
    Route::get('create', 'PackagesController@create')->name('packages.create');
    Route::post('save', 'PackagesController@save')->name('packages.save');
    Route::get('edit/{id}', 'PackagesController@edit')->name('packages.edit');
    Route::post('update/{id}', 'PackagesController@update')->name('packages.update');
    Route::post('delete', 'PackagesController@delete')->name('packages.delete');
});

/**
 * customers
 */
Route::prefix('customer')->group(function () {
    Route::any('index', 'CustomerController@index')->name('customer.index');
    Route::get('create', 'CustomerController@create')->name('customer.create');
    Route::post('save', 'CustomerController@save')->name('customer.save');
    Route::get('edit/{id}', 'CustomerController@edit')->name('customer.edit');
    Route::post('update/{id}', 'CustomerController@update')->name('customer.update');
    Route::post('delete', 'CustomerController@delete')->name('customer.delete');
});

Route::post('get-states', 'PackagesController@getStates')->name('get.states');
Route::post('add-city', 'PackagesController@addCity')->name('add.city');