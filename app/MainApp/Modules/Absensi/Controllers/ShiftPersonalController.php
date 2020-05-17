<?php

namespace App\MainApp\Modules\Absensi\Controllers;

use Illuminate\Http\Request;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Facades\App\MainApp\Repositories\Kepegawaian;
use App\Base\BaseController;
use Facades\App\MainApp\Repositories\Absensi;
use Facades\App\MainApp\Repositories\Master;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ShiftPersonalController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }

    public function index(Request $request)
    {        
        // jika login pegawai maka tolak
        if(Kepegawaian::CurOnline()!=false){
            $this->response = redirect()->route('shift_personal.index');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        if(!UserAuth::hasAccess('Absensi.shift')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'absensi.setshift';
        $orderBy = false;
        $filter = //untuk filter ke repo
        $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null)
            ];
        $this->output['data']['q'] = $filter['q'];
        $this->output['data']['hariList'] = [
            '',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu'
        ];
        unset($this->output['data']['hariList'][0]);
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
        unset($this->output['data']['bulanList'][0]);
                
        $nextYear = now()->addYear()->format('Y');
        $this->output['data']['tahunList'] = [];
        
        for ($year=config('AppConfig.packageLocal.Absensi.year_start',2019);$year<=$nextYear;$year++) {
            $this->output['data']['tahunList'][$year] = $year;
        }

        $this->output['data']['bulan'] = now()->format('m');
        $this->output['data']['tahun'] = now()->format('Y');
        
        if($request->input('bulan',false)){
            $this->output['data']['bulan'] = $request->input('bulan');
            $this->output['data']['bulan'] = (int) ltrim($this->output['data']['bulan'],'0');
            if($this->output['data']['bulan']<=9)$this->output['data']['bulan'] = '0'.$this->output['data']['bulan'];            
        }

        $paginationParams['bulan'] =$this->output['data']['bulan'];
        if($request->input('tahun',false)){
            $this->output['data']['tahun'] = $request->input('tahun');
        }
        $paginationParams['tahun'] =$this->output['data']['tahun'];
        
        $startDate = new Carbon($this->output['data']['tahun'].'-'.$this->output['data']['bulan'].'-01');
        $this->output['data']['daysInMonth'] = $startDate->daysInMonth;

        $filter['tanggal_start'] = $startDate->format('Y-m-d');
        $filter['tanggal_end'] = $startDate->format('Y-m-').$this->output['data']['daysInMonth'];
        
        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 10);

        if(UserAuth::getActiveUserRoleCode()=='pejabat_approval'){
            $filter[] = ['instansi_id', UserAuth::user('profile')['instansi_id']];
        }else if(UserAuth::getActiveUserRoleCode()=='admin_satker'){
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                if(isset($instansiId[1]))$instansiId = $instansiId[1];
            }
            $filter[] = [
                ['instansi_id', $instansiId],['OR instansi_induk_path', 'LIKE', '%;'.$instansiId.';%']
            ];            
        }
        
        $this->output['data']['pegawai'] = Kepegawaian::listPegawaiAbsensi(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );
        // dd($this->output['data']['pegawai']);

        // $pegawaiIds = [];
        // foreach($this->output['data']['pegawai']['data'] as $peg) {
        //     $pegawaiIds[] = $peg['id'];
        // }

        $hariLibur = Master::listHariLibur([
            'start' => $filter['tanggal_start'],
            'end' => $filter['tanggal_end']
        ]);
        $libur = [];
        
        foreach ($hariLibur['data'] as $key => $value) {
            $period = CarbonPeriod::create($value['start'], $value['end']);
            foreach ($period as $val) {
                $a = $val->format('Y-m-d');
                if(!isset($libur[$a]))
                    $libur[$a] = '';
                $libur[$a] .= '<li>'.$value['title'].'</li>';                
            }
        }
        $this->output['data']['hariLibur'] = $libur;

        $shift = Master::listShift();
        $this->output['data']['shift'] = $shift['data'];
        
        //generate pagination
        $this->output['viewdata']['pagination'] = Kepegawaian::getPaginationPegawai(route('shift_personal.index', $paginationParams));
        $this->output['data']['query'] = http_build_query($request->except(['limit','offset','page']));
        return $this->done();
    }
    
    /**
     * set / generate default absensi/shift seluruh pegawai (yg dibawahi oleh user login) di bulan yg terpilih
     */
    public function setDefaultAbsensi(Request $request)
    { 
        $backParam = [];
        $this->response = redirect()->route('shift_personal.index');

        // jika login pegawai maka tolak
        if(Kepegawaian::CurOnline()!=false){
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }
        
        $this->output['data']['bulan'] = now()->format('m');
        $this->output['data']['tahun'] =  now()->format('Y');
        
        if($request->input('bulan',false)){
            $this->output['data']['bulan'] = $request->input('bulan');
            $this->output['data']['bulan'] = (int) ltrim($this->output['data']['bulan'],'0');
            if($this->output['data']['bulan']<=9)$this->output['data']['bulan'] = '0'.$this->output['data']['bulan'];            
        }
        if($request->input('tahun',false)){
            $this->output['data']['tahun'] = $request->input('tahun');
        }

        $filter = [];
        if($q = $request->input('q', null)){
            $filter[] = [
                ['nama','LIKE','%'.$q.'%'],
                ['OR kode','LIKE','%'.$q.'%'],
                ['OR ktp','LIKE','%'.$q.'%'],
                ['OR npwp','LIKE','%'.$q.'%']
            ];
        }
        if(UserAuth::getActiveUserRoleCode()=='pejabat_approval'){
            $filter[] = ['instansi_id', UserAuth::user('profile')['instansi_id']];
        }else if(UserAuth::getActiveUserRoleCode()=='admin_satker'){
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                if(isset($instansiId[1]))$instansiId = $instansiId[1];
            }
            $filter[] = ['instansi_id', $instansiId];
            $filter[] = ['OR instansi_induk_path', 'LIKE', '%;'.$instansiId.';%'];            
        }
        
        $tgl = new Carbon($this->output['data']['tahun'].'-'.$this->output['data']['bulan'].'-01');
        $jumlahHari = $tgl->daysInMonth;
        $startDate = $tgl->format('Y-m-d');
        $endDate = $tgl->format('Y-m-').$jumlahHari;
        \App\MainApp\Jobs\GenerateAbsensiDefault::dispatch(
            $filter,
            $startDate,
            $endDate            
        );
        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $backParam['orderBy'] =$orderBy[0];
            $backParam['orderType'] =$orderBy[1];
        }
        
        
        $backParam['bulan'] = $this->output['data']['bulan'];
        $backParam['tahun'] = $this->output['data']['tahun'];
        $backParam['offset'] = $request->input('offset', 0);
        $backParam['limit'] = $request->input('limit', 10);
        $backParam['q'] = $request->input('q', '');

        $this->response = redirect()->route('shift_personal.index',$backParam);

        $this->setAlert('Set shift default kepada pegawai sedang diproses, tunggu beberapa saat hingga proses selesai.','success'); 

        return $this->done();
    }
    
    /**
     * set shift per pegawai
     */
    public function update(Request $request)
    {
        $backParam = [];
        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $backParam['orderBy'] =$orderBy[0];
            $backParam['orderType'] =$orderBy[1];
        }    
        $backParam['offset'] = $request->input('offset', 0);
        $backParam['limit'] = $request->input('limit', 10);
        $backParam['tahun'] = $request->input('tahun', now()->format('Y'));
        $backParam['bulan'] = $request->input('bulan', now()->format('m'));
        $backParam['q'] = $request->input('q', '');
        $this->response = redirect()->route('shift_personal.index',$backParam);

        // jika login pegawai maka tolak
        if(Kepegawaian::CurOnline()!=false){
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }        
        $now = now()->format('Y-m-d');
        $pegawaiId = $request->input('pegawai_id',0);
        $startDate = $request->input('start',$now);
        $endDate = $request->input('end',$now);
        if(!$pegawaiId){
            $this->setAlert('Parameter pegawai tidak terdeteksi','danger'); 
            return $this->done();
        }
        
        // if($startDate < $now || $endDate < $now){
        //     $this->setAlert('Tanggal yang dipilih keliru','danger'); 
        //     return $this->done();
        // }
        
        $shiftId = $request->input('shift_id',0);
        $libur = $request->input('libur',0);

        if($libur){
            $result = Absensi::setLibur($pegawaiId,$startDate,$endDate);
        }else{
            $result = Absensi::generateAbsensi(
                $pegawaiId,$shiftId,$startDate,$endDate,true          
            );
        }   
        
        if($result){
            $this->setAlert('Data berhasil diproses.','success'); 
        }else{            
            $this->setAlert('Update Failed : '.Absensi::error(),'warning'); 
        }

        return $this->done();
    }
    

}
