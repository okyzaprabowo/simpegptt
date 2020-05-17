<?php
Route::put('/autoprocess', 'AbsensiController@autoProcess');
// Route::middleware('auth:api')->group(function(){
//     Route::post('api1', 'AbsensiController@api1');
// });

// Route::group(['prefix'=>'permohonan_absen'],function(){  
//     Route::get('/', 'PermohonanAbsenController@index')->name('absensi.permohonan_absen.api.list');
//     Route::post('/', 'PermohonanAbsenController@create')->name('absensi.permohonan_absen.api.create');
//     Route::get('/{id}', 'PermohonanAbsenController@edit')->name('absensi.permohonan_absen.api.edit');
//     Route::put('/{id}', 'PermohonanAbsenController@update')->name('absensi.permohonan_absen.api.update');
//     Route::put('/approval/{id}', 'PermohonanAbsenController@approved')->name('absensi.permohonan_absen.api.approved');
//     Route::delete('/{id}', 'PermohonanAbsenController@delete')->name('absensi.permohonan_absen.api.delete');

$group = [
    // 'prefix' => config('AppConfig.endpoint.admin.Absensi'),
   'middleware' => 'auth:api'
];
Route::group($group,function(){ 
    
    Route::group(['prefix'=>'upload'],function(){ 
        //form upload & list file uploaded
        Route::get('/', 'AbsensiController@index')->name('absensi_upload.api');
        //prosess upload
        Route::post('/', 'AbsensiController@create')->name('absensi_upload.api.create');
        //proses kalkulasi hasil import raw absen ke table absense
        Route::put('/process', 'AbsensiController@processRaw')->name('absensi_upload.api.process');
        Route::delete('/{id}', 'AbsensiController@delete')->name('absensi_upload.api.delete');
        //detail dan list raw data
        Route::get('/{id}', 'AbsensiController@detail')->name('absensi_upload.detail.api');
        Route::delete('/detail/{id}', 'AbsensiController@detailDelete')->name('absensi_upload.detail.api.delete');
    });

    Route::group(['prefix'=>'permohonan'],function(){  
        Route::get('/', 'PermohonanAbsenController@index')->name('absensi.permohonan_absen.api.index');
        //form add
        Route::get('/approval', 'PermohonanAbsenController@approval')->name('permohonan_absen.api.approval');
        Route::put('/approve/{id}', 'PermohonanAbsenController@approve')->name('permohonan_absen.api.approve');
        Route::get('/add', 'PermohonanAbsenController@addNew')->name('permohonan_absen.api.addNew');
        Route::post('/', 'PermohonanAbsenController@create')->name('permohonan_absen.api.create');
        Route::get('/{id}', 'PermohonanAbsenController@edit')->name('permohonan_absen.api.edit');
        Route::put('/{id}', 'PermohonanAbsenController@update')->name('permohonan_absen.api.update');
        Route::delete('/{id}', 'PermohonanAbsenController@delete')->name('permohonan_absen.api.delete');

    });    
    
    Route::group(['prefix'=>'shift'],function(){  
        Route::get('/', 'ShiftPersonalController@index')->name('shift_personal.api.index');
        //generate absensi
        Route::get('/set-default-absensi', 'ShiftPersonalController@setDefaultAbsensi')->name('shift_personal.api.setDefaultAbsensi');
        Route::put('/set-absensi', 'ShiftPersonalController@update')->name('shift_personal.api.update');
    });
});
