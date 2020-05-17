<?php
Route::middleware('auth:api')->group(function(){
    Route::post('api1', 'DashboardController@api1');
});