<?php

$homeSlug = trim(config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.home_slug',''),'/');
$group = [
    'prefix' => str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.admin.Pegawai')),
    'middleware' => 'auth'
];

Route::group($group,function(){  
    // Route::group(['prefix'=>'pegawai'],function(){
        
        //my profile        
        Route::get('/profile', 'PegawaiController@profile')->name('pegawai.profile');
        Route::put('/profile', 'PegawaiController@updateProfile')->name('pegawai.profile');

        Route::get('/', 'PegawaiController@index')->name('pegawai.list');
        //form add
        Route::get('/add', 'PegawaiController@addNew')->name('pegawai.addNew');
        Route::post('/', 'PegawaiController@create')->name('pegawai.create');
        Route::get('/{id}', 'PegawaiController@edit')->name('pegawai.edit');
        Route::get('/{id}/view', 'PegawaiController@view')->name('pegawai.view');
        Route::put('/{id}', 'PegawaiController@update')->name('pegawai.update');
        Route::delete('/{id}', 'PegawaiController@delete')->name('pegawai.delete');

    // });
});