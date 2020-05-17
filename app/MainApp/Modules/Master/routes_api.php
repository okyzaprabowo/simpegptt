<?php

$group = [
    // 'prefix' => config('AppConfig.endpoint.admin.Master'),
    'middleware' => 'auth:api' // ceuk obozz ditutup hula
];
Route::group($group, function(){

    Route::group(['prefix'=>'jenis_ijin'],function(){  
        Route::get('/', 'JenisIjinController@index')->name('master.jenis_ijin.api.list');
        Route::post('/', 'JenisIjinController@create')->name('master.jenis_ijin.api.create');
        Route::get('/{id}', 'JenisIjinController@edit')->name('master.jenis_ijin.api.edit');
        Route::put('/{id}', 'JenisIjinController@update')->name('master.jenis_ijin.api.update');
        Route::delete('/{id}', 'JenisIjinController@delete')->name('master.jenis_ijin.api.delete');
    });
    Route::group(['prefix'=>'jenis_ijin_kategori'],function(){  
        Route::get('/', 'JenisIjinController@kategoriIndex')->name('master.jenis_ijin_kategori.api.list');
        //form add
        Route::get('/add', 'JenisIjinController@kategoriaddNew')->name('master.jenis_ijin_kategori.api.addNew');
        Route::post('/', 'JenisIjinController@kategoriCreate')->name('master.jenis_ijin_kategori.api.create');
        Route::get('/{id}', 'JenisIjinController@kategoriEdit')->name('master.jenis_ijin_kategori.api.edit');
        Route::put('/{id}', 'JenisIjinController@kategoriUpdate')->name('master.jenis_ijin_kategori.api.update');
        Route::delete('/{id}', 'JenisIjinController@kategoriDelete')->name('master.jenis_ijin_kategori.api.delete');        
    });
    Route::group(['prefix'=>'jabatan'],function(){  
        Route::get('/', 'JabatanController@index')->name('master.jabatan.api.list');
        Route::post('/', 'JabatanController@create')->name('master.jabatan.api.create');
        Route::get('/{id}', 'JabatanController@edit')->name('master.jabatan.api.edit');
        Route::put('/{id}', 'JabatanController@update')->name('master.jabatan.api.update');
        Route::delete('/{id}', 'JabatanController@delete')->name('master.jabatan.api.delete');
    });
    Route::group(['prefix'=>'instansi'],function(){  
        Route::get('/', 'InstansiController@index')->name('master.instansi.api.list');
        Route::post('/', 'InstansiController@create')->name('master.instansi.api.create');
        Route::get('/{id}', 'InstansiController@edit')->name('master.instansi.api.edit');
        Route::put('/{id}', 'InstansiController@update')->name('master.instansi.api.update');
        Route::delete('/{id}', 'InstansiController@delete')->name('master.instansi.api.delete');
    });

    Route::group(['prefix'=>'mesin_absen'],function(){  
        Route::get('/', 'MesinAbsenController@index')->name('master.mesin_absen.api.list');
        Route::post('/', 'MesinAbsenController@create')->name('master.mesin_absen.api.create');
        Route::get('/{id}', 'MesinAbsenController@edit')->name('master.mesin_absen.api.edit');
        Route::put('/{id}', 'MesinAbsenController@update')->name('master.mesin_absen.api.update');
        Route::delete('/{id}', 'MesinAbsenController@delete')->name('master.mesin_absen.api.delete');
    });


    Route::group(['prefix'=>'hari_libur'],function(){  
        Route::get('/', 'HariLiburController@index')->name('master.hari_libur.api.list');
        //form add
        // Route::get('/add', 'HariLiburController@addNew')->name('master.hari_libur.api.addNew');
        Route::post('/', 'HariLiburController@create')->name('master.hari_libur.api.create');
        // Route::get('/{id}', 'HariLiburController@edit')->name('master.hari_libur.api.edit');
        // Route::put('/{id}', 'HariLiburController@update')->name('master.hari_libur.api.update');
        Route::delete('/{id}', 'HariLiburController@delete')->name('master.hari_libur.api.api.delete');
    });

    Route::group(['prefix'=>'shift'],function(){  
        Route::get('/', 'ShiftController@index')->name('master.shift.api.list');
        Route::post('/', 'ShiftController@create')->name('master.shift.api.create');
        Route::post('/detail', 'ShiftController@createDetail')->name('master.shift.api.createDetail');
        Route::put('/detail/{id}', 'ShiftController@updateDetail')->name('master.shift.api.updateDetail');
        Route::get('/{id}', 'ShiftController@edit')->name('master.shift.api.edit');
        Route::put('/{id}', 'ShiftController@update')->name('master.shift.api.update');
        Route::delete('/{id}', 'ShiftController@delete')->name('master.shift.api.delete');
    });
    
});
