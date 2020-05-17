<?php

use Carbon\CarbonPeriod;
use Carbon\Carbon;
use Facades\App\MainApp\Repositories\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

$group = [
    // 'prefix' => config('AppConfig.endpoint.api.Pegawai'),
    'middleware' => 'auth'
];
Route::group($group, function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
});


Route::get('/deletedoubleentry', function(Request $request){	
    $absensi = DB::table('absensi_tmp')->get();
    foreach ($absensi as $value) {
        echo $value->id.' - ';
        DB::table('absensi')->where('id',$value->id)->delete();
    }
});

Route::get('/runJobs/InsertFromFileRaw/{uploadId}', function(Request $request){	
	\App\MainApp\Jobs\InsertFromFileRaw::dispatch($request->route('uploadId'));
});

Route::get('/update/run-artisan/{action}', function (Request $request) {
    $shellOnlyCommands = [
        'clear-compiled',
        'package:discover',
        'backup:run',
        'passport:client --password',
        'passport:install',
        'apidoc:generate',
        'route:list',
        'config:cache',
        'config:clear',
        'migrate',
        'db:seed',
        'route:cache',
        'route:clear',
        'view:cache',
        'view:clear',
        'optimize'
    ];
    $command = $request->route('action');
    $ret = '';
    if (in_array($command, $shellOnlyCommands)) {
        $ret = shell_exec('cd ' . base_path('') . ' && php artisan ' . $command);
    } else {
        Artisan::call($command);
        $ret = Artisan::output();
    }

    return ['status' => 200, 'action' => 'php artisan ' . $command, 'return' => $ret];
});

// Route::get('/update/run-artisan/{action}', function(Request $request){
//     Artisan::call($request->route('action'));
//     $ret = Artisan::output();
//     return ['status'=>200,'action'=>'php artisan '.$request->route('action'),'return'=>$ret];
// });

Route::get('/testingcuy', function () {
    dd(Absensi::kalkulasiAbsensi(3));
    $date = new Carbon('2019-12-19');
    dd($date->lessThan(now()->format('Y-m-d')));
    $update = [
        'status' => 0,
        'jenis_ijin_id' => 0,
        'scan_masuk' => null,
        'scan_keluar' => null,

        'kelebihan_jam' => 0,
        'keterlambatan_jam' => 0,
        'pulang_cepat_jam' => 0,

        'total_jam' => 0,
        'kekurangan_jam' => 0,
    ];

    $update['scan_masuk'] = '2019-12-03 14:55:01';
    $update['scan_keluar'] = '2019-12-03 16:02:17';
    $absen = [
        'data' => [
            'jam_masuk' => '2019-12-03 08:00:00',
            'jam_keluar' => '2019-12-03 17:00:00'
        ]
    ];

    $jamKerjaMasuk = new Carbon($absen['data']['jam_masuk']);
    $jamKerjaKeluar = new Carbon($absen['data']['jam_keluar']);

    //total seharusnya jam kerja
    $totalJamKerja =  $jamKerjaMasuk->diffInSeconds($absen['data']['jam_keluar'], false);

    $tengahAbsen = (new Carbon($absen['data']['jam_masuk']))->addSecond(ceil($totalJamKerja / 2))->format('Y-m-d H:i:s');

    //cek pastikan scan masuk memang di waktu yg untuk scan masuk
    if ($update['scan_masuk'] !== null && !($update['scan_masuk'] <= $tengahAbsen)) {
        $update['scan_masuk'] = null;
    }

    //cek pastikan scan keluar memang di waktu yg untuk scan keluar
    if ($update['scan_keluar'] !== null && !($update['scan_keluar'] > $tengahAbsen)) {
        $update['scan_masuk'] = null;
    }

    if (isset($update['scan_keluar'])) {

        $jamScanMasuk = new Carbon($update['scan_masuk']);
        $jamScanKeluar = new Carbon($update['scan_keluar']);

        //total realisasi jam kerja hari ini, dari scan masuk ke scan keluar
        $update['total_jam'] = $jamScanMasuk->diffInSeconds($update['scan_keluar'], false);
        // $update['total_jam'] = ($update['total_jam']/60);

        //dari jam masuk ke scan masuk
        if ($jamScanMasuk->greaterThan($jamKerjaMasuk))
            $update['keterlambatan_jam'] = $jamKerjaMasuk->diffInSeconds($update['scan_masuk'], false);

        //dari scan keluar ke jam keluar
        if ($jamScanKeluar->lessThan($jamKerjaKeluar))
            $update['pulang_cepat_jam'] = $jamScanKeluar->diffInSeconds($absen['data']['jam_keluar'], false);

        //kekurangan jam kerja, dari total jam kerja ke total seharusnya kerja
        if ($totalJamKerja > $update['total_jam']) {
            $update['kekurangan_jam'] = $totalJamKerja - $update['total_jam'];

            //kelebihan jam kerja, dari total masuk ke total jam kerja
        } else if ($update['total_jam'] > $totalJamKerja) {
            $update['kelebihan_jam'] = $update['total_jam'] - $totalJamKerja;
        }

        if ($update['keterlambatan_jam']) {
            $update['status'] = 3; //telat
        } else {
            $update['status'] = 1; //hadir
        }
    } else {
        $update['status'] = 3; //telat/kurang scan
    }
    dd($update);
    return $update;
});

Route::get('/', function () {
    return redirect()->route('auth.login');
});
