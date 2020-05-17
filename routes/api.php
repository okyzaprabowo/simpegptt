<?php

// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

$group = [
    // 'prefix' => config('AppConfig.endpoint.api.Pengajuan'),
    'middleware' => 'auth:api'
];
Route::group($group,function(){  
    /**
     * Config
     */
    Route::get(config('AppConfig.system.config_endpoint'), 'ConfigController@readList');
    //create atau update config
    Route::post(config('AppConfig.system.config_endpoint'), 'ConfigController@createUpdate');
});
