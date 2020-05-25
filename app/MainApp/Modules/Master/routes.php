<?php

$homeSlug = trim(config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.home_slug',''),'/');
$group = [
    'prefix' => str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.admin.Master')),
    'middleware' => 'auth'
];

Route::group($group,function(){  
    Route::group(['prefix'=>'jenis_ijin'],function(){  
        Route::get('/', 'JenisIjinController@index')->name('master.jenis_ijin.list');
        //form add
        Route::get('/add', 'JenisIjinController@addNew')->name('master.jenis_ijin.addNew');
        Route::post('/', 'JenisIjinController@create')->name('master.jenis_ijin.create');
        Route::get('/{id}', 'JenisIjinController@edit')->name('master.jenis_ijin.edit');
        Route::put('/{id}', 'JenisIjinController@update')->name('master.jenis_ijin.update');
        Route::delete('/{id}', 'JenisIjinController@delete')->name('master.jenis_ijin.delete');        
    });
    Route::group(['prefix'=>'jenis_ijin_kategori'],function(){  
        Route::get('/', 'JenisIjinController@kategoriIndex')->name('master.jenis_ijin_kategori.list');
        //form add
        Route::get('/add', 'JenisIjinController@kategoriaddNew')->name('master.jenis_ijin_kategori.addNew');
        Route::post('/', 'JenisIjinController@kategoriCreate')->name('master.jenis_ijin_kategori.create');
        Route::get('/{id}', 'JenisIjinController@kategoriEdit')->name('master.jenis_ijin_kategori.edit');
        Route::put('/{id}', 'JenisIjinController@kategoriUpdate')->name('master.jenis_ijin_kategori.update');
        Route::delete('/{id}', 'JenisIjinController@kategoriDelete')->name('master.jenis_ijin_kategori.delete');        
    });

    Route::group(['prefix'=>'jabatan'],function(){  
        Route::get('/', 'JabatanController@index')->name('master.jabatan.list');
        //form add
        Route::get('/add', 'JabatanController@addNew')->name('master.jabatan.addNew');
        Route::post('/', 'JabatanController@create')->name('master.jabatan.create');
        Route::get('/{id}', 'JabatanController@edit')->name('master.jabatan.edit');
        Route::put('/{id}', 'JabatanController@update')->name('master.jabatan.update');
        Route::delete('/{id}', 'JabatanController@delete')->name('master.jabatan.delete');
    });

    Route::group(['prefix'=>'instansi'],function(){  
        Route::get('/', 'InstansiController@index')->name('master.instansi.list');
        //form add
        Route::get('/add', 'InstansiController@addNew')->name('master.instansi.addNew');
        Route::post('/', 'InstansiController@create')->name('master.instansi.create');
        Route::get('/{id}', 'InstansiController@edit')->name('master.instansi.edit');
        Route::put('/{id}', 'InstansiController@update')->name('master.instansi.update');
        Route::delete('/{id}', 'InstansiController@delete')->name('master.instansi.delete');
    });
    
    Route::group(['prefix'=>'hari_libur'],function(){  
        Route::get('/', 'HariLiburController@index')->name('master.hari_libur.list');
        //form add
        // Route::get('/add', 'JabatanController@addNew')->name('master.jabatan.addNew');
        Route::post('/', 'HariLiburController@create')->name('master.hari_libur.create');
        // Route::get('/{id}', 'JabatanController@edit')->name('master.jabatan.edit');
        // Route::put('/{id}', 'JabatanController@update')->name('master.jabatan.update');
        // Route::delete('/{id}', 'JabatanController@delete')->name('master.jabatan.delete');
    });

    Route::group(['prefix'=>'mesin_absen'],function(){  
        Route::get('/', 'MesinAbsenController@index')->name('master.mesin_absen.list');
        //form add
        Route::get('/add', 'MesinAbsenController@addNew')->name('master.mesin_absen.addNew');
        Route::post('/', 'MesinAbsenController@create')->name('master.mesin_absen.create');
        Route::get('/{id}', 'MesinAbsenController@edit')->name('master.mesin_absen.edit');
        Route::put('/{id}', 'MesinAbsenController@update')->name('master.mesin_absen.update');
        Route::delete('/{id}', 'MesinAbsenController@delete')->name('master.mesin_absen.delete');
    });

    Route::group(['prefix'=>'shift'],function(){  
        Route::get('/', 'ShiftController@index')->name('master.shift.list');
        //form add
        Route::get('/add', 'ShiftController@addNew')->name('master.shift.addNew');
        Route::get('/detail/{id}', 'ShiftController@detail')->name('master.shift.detail');
        Route::post('/', 'ShiftController@create')->name('master.shift.create');
        Route::post('/detail/', 'ShiftController@create')->name('master.shift.createDetail');
        Route::get('/{id}', 'ShiftController@edit')->name('master.shift.edit');
        Route::put('/{id}', 'ShiftController@update')->name('master.shift.update');
        Route::delete('/{id}', 'ShiftController@delete')->name('master.shift.delete');
    });

    Route::group(['prefix'=>'waktu_absen'],function(){  
        Route::get('/', 'WaktuAbsenController@index')->name('master.waktu_absen.list');
        Route::put('/', 'WaktuAbsenController@update')->name('master.waktu_absen.update');
    });
});
  