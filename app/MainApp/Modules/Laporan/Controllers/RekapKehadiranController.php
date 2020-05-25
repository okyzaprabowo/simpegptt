<?php

namespace App\MainApp\Modules\Laporan\Controllers;

use Illuminate\Http\Request;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Facades\App\MainApp\Repositories\Kepegawaian;
use Facades\App\MainApp\Repositories\Absensi;
use Facades\App\MainApp\Repositories\Master;
use App\Base\BaseController;
use Carbon\Carbon;

class RekapKehadiranController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }

    public function index(Request $request)
    {
        if(!UserAuth::hasAccess('Laporan.rekapkehadiran')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'laporan.rekap_kehadiran';
        $this->output['data']['bulanList'] = [
            '',
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
        
        $this->output['data']['tanggal_start'] = now()->subMonth()->format('Y-m-d');
        $this->output['data']['tanggal_end'] = now()->format('Y-m-d');
        
        if($request->input('tanggal_start',false)){
            $this->output['data']['tanggal_start'] = $request->input('tanggal_start');
        }
        
        if($request->input('tanggal_end',false)){
            $this->output['data']['tanggal_end'] = $request->input('tanggal_end');
        }
        // Absensi::addTime('18:00:00','15:12:00');
        //--------------------------
        $orderBy = false;
        $filter = ['q'=>$request->input('q', null)];//untuk filter ke repo
        $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null),
                'tanggal_start' => $this->output['data']['tanggal_start'],
                'tanggal_end' => $this->output['data']['tanggal_end']
            ];

        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 10);

        if(UserAuth::getActiveUserRoleCode()=='pejabat_approval'){
            
            $instansiId = UserAuth::user('profile')['instansi_id'];

        }else if(UserAuth::getActiveUserRoleCode()=='admin_satker' || UserAuth::getActiveUserRoleCode()=='pimpinan2'){
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                //$instansiId[1] = eselon 2, jika tidak ada berarti eselon 2 adalah instansi yg dipilih
                if(isset($instansiId[1])){
                    $instansiId = $instansiId[1];
                }else{
                    $instansiId = UserAuth::user('profile')['instansi_id'];
                }
            }
        }else if(UserAuth::getActiveUserRoleCode()=='pimpinan3') {
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                //$instansiId[2] == eselon 3, jika tidak ada berarti eselon 3 adalah instansi yg dipilih
                if(isset($instansiId[2])){
                    $instansiId = $instansiId[2];
                }else{
                    $instansiId = UserAuth::user('profile')['instansi_id'];
                }
            }
        }

        if(isset($instansiId)){
            $filter[] = [['instansi_id', $instansiId],['OR instansi_induk_path', 'LIKE', '%;'.$instansiId.';%']];
        }
        
        // only is_enable true
        $filter[] = ['is_enable', 1];

        $this->output['data']['pegawai'] = Kepegawaian::listPegawai(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        $pegawaiIds = [];
        foreach($this->output['data']['pegawai']['data'] as $peg) {
            $pegawaiIds[] = $peg['id'];
        }
        
        //----------------------
        $jenisIjin = Master::listJenisIjin();
        $this->output['data']['jenisIjin'] = [];
        $this->output['data']['jenisIjinKategori'] = [0=>['jenisIjin'=>[],'kategori'=>[]]];
        foreach($jenisIjin['data'] as $val) {
            $this->output['data']['jenisIjin'][$val['id']] = $val;
            if(!isset($this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']]))
                $this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']] = ['jenisIjin'=>[],'kategori'=>$val['kategori']];
            $this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']]['jenisIjin'][$val['id']] = $val;
        }
        $this->output['data']['absensi'] = Absensi::rekapAbsensi(
            $pegawaiIds,$this->output['data']['tanggal_start'], $this->output['data']['tanggal_end']
        );
        // dd($this->output['data']['absensi']);
        
        //generate pagination
        $this->output['viewdata']['pagination'] = Kepegawaian::getPaginationPegawai(route('laporan.rekap_kehadiran', $paginationParams));
        
        return $this->done();
    }
}
