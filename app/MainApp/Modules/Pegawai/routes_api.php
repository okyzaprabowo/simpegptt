<?php
$group = [
    // 'prefix' => config('AppConfig.endpoint.api.Pegawai'),
      'middleware' => 'auth:api'
];
Route::group($group,function(){  
    
    
    //alamat
    Route::group(['prefix'=>'alamat'],function(){  
        Route::get('/', 'AlamatController@index')->name('pegawai.alamat.api.list');
        Route::post('/', 'AlamatController@create')->name('pegawai.alamat.api.create');
        Route::get('/{id}', 'AlamatController@edit')->name('pegawai.alamat.api.edit');
        Route::put('/{id}', 'AlamatController@update')->name('pegawai.alamat.api.update');
        Route::delete('/{id}', 'AlamatController@delete')->name('pegawai.alamat.api.delete');
    });

    //keluarga
    Route::group(['prefix'=>'keluarga'],function(){  
        Route::get('/', 'KeluargaController@index')->name('pegawai.keluarga.api.list');
        Route::post('/', 'KeluargaController@create')->name('pegawai.keluarga.api.create');
        Route::get('/{id}', 'KeluargaController@edit')->name('pegawai.keluarga.api.edit');
        Route::put('/{id}', 'KeluargaController@update')->name('pegawai.keluarga.api.update');
        Route::delete('/{id}', 'KeluargaController@delete')->name('pegawai.keluarga.api.delete');
    });

    //pendidikan
    Route::group(['prefix'=>'pendidikan'],function(){  
        Route::get('/', 'PendidikanController@index')->name('pegawai.pendidikan.api.list');
        Route::post('/', 'PendidikanController@create')->name('pegawai.pendidikan.api.create');
        Route::get('/{id}', 'PendidikanController@edit')->name('pegawai.pendidikan.api.edit');
        Route::put('/{id}', 'PendidikanController@update')->name('pegawai.pendidikan.api.update');
        Route::delete('/{id}', 'PendidikanController@delete')->name('pegawai.pendidikan.api.delete');
    });


    // Route::get('/jabatan', 'PegawaiController@jabatanList')->name('pegawai.jabatanList'); 
    // Route::get('/divisi', 'PegawaiController@divisiList')->name('pegawai.divisiList'); 

    /**
     * [pegawai]
     * -----------------
     */
    //list 
    Route::get('/', 'PegawaiController@index')->name('pegawai.api.readList'); 
    //get 1 mitra
    Route::get('/{id}', 'PegawaiController@edit')->name('pegawai.api.edit');
    //tambah 1 
    Route::post('/', 'PegawaiController@create')->name('pegawai.api.create');  
    //update 
    Route::put('/{id}', 'PegawaiController@update')->name('pegawai.api.update'); 
    Route::put('/{id}/suspend', 'PegawaiController@suspend')->name('pegawai.api.suspend'); 
    Route::put('/{id}/updatepassword', 'PegawaiController@updatePassword')->name('pegawai.api.updatePassword'); 
    //delete 
    Route::delete('/{id}', 'PegawaiController@delete')->name('pegawai.api.delete');
    
    
    
});