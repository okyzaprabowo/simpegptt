<?php

$homeSlug = trim(config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.home_slug',''),'/');
$group = [
    'prefix' => str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.admin.Laporan')),
    'middleware' => 'auth'
];

Route::group($group,function(){  
    Route::get('kehadiran_harian', 'KehadiranHarianController@index')->name('laporan.kehadiran_harian');
    Route::get('rekap_kehadiran', 'RekapKehadiranController@index')->name('laporan.rekap_kehadiran');
    Route::get('jejak_kehadiran', 'JejakKehadiranController@index')->name('laporan.jejak_kehadiran');        
});