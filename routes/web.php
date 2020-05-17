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

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

// Route::get('/{any}', 'ApplicationController')->where('any', '.*');

/**
 * languange
 */
// /getlang/lang.js
Route::get(config('AppConfig.system.lang_endpoint'),'LangController@readList');

/**
 * Tenant
 */
// /gettenant/tenant.js
Route::get(config('AppConfig.system.web_admin.multitenant.api_endpoint.tenant'),'TenantController@readList');
Route::get(config('AppConfig.system.web_admin.multitenant.api_endpoint.tenant_group'),'TenantController@tenantGroupList');