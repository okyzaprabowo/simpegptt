<?php

$homeSlug = trim(config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.home_slug',''),'/');
$group = [
    'prefix' => str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.admin.Absensi')),
    'middleware' => 'auth'
];

Route::group($group,function(){ 
    
    Route::group(['prefix'=>'upload'],function(){ 
        //form upload & list file uploaded
        Route::get('/', 'AbsensiController@index')->name('absensi_upload');
        //prosess upload
        Route::post('/', 'AbsensiController@create')->name('absensi_upload.create');
        Route::delete('/{id}', 'AbsensiController@delete')->name('absensi_upload.delete');
        //detail dan list raw data
        Route::get('/{id}', 'AbsensiController@detail')->name('absensi_upload.detail');
        Route::delete('/detail/{id}', 'AbsensiController@detailDelete')->name('absensi_upload.detail.delete');
    });

    Route::group(['prefix'=>'permohonan'],function(){  
        Route::get('/', 'PermohonanAbsenController@index')->name('permohonan_absen.index');
        //form add
        Route::get('/approval', 'PermohonanAbsenController@approval')->name('permohonan_absen.approval');
        // Route::put('/approve/{id}', 'PermohonanAbsenController@addNew')->name('permohonan_absen.approve');
        Route::get('/add', 'PermohonanAbsenController@addNew')->name('permohonan_absen.addNew');
        Route::post('/', 'PermohonanAbsenController@create')->name('permohonan_absen.create');
        Route::get('/{id}', 'PermohonanAbsenController@edit')->name('permohonan_absen.edit');
        Route::put('/{id}', 'PermohonanAbsenController@update')->name('permohonan_absen.update');
        Route::delete('/{id}', 'PermohonanAbsenController@delete')->name('permohonan_absen.delete');
    });

    
    Route::group(['prefix'=>'shift'],function(){  
        Route::get('/', 'ShiftPersonalController@index')->name('shift_personal.index');
        //generate absensi
        Route::get('/set-default-absensi', 'ShiftPersonalController@setDefaultAbsensi')->name('shift_personal.setDefaultAbsensi');
        Route::put('/set-absensi', 'ShiftPersonalController@update')->name('shift_personal.update');
    });
});