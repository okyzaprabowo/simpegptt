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
use Carbon\CarbonPeriod;
class JejakKehadiranController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }

    public function index(Request $request)
    {
        if(!UserAuth::hasAccess('Laporan.jejakkehadiran')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }
        
        $this->response = 'laporan.jejak_kehadiran';

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

        $this->output['data']['isPegawai'] = true;
        $this->output['data']['pegawaiId'] = 0;
        $this->output['data']['bulan'] = now()->format('m');
        $this->output['data']['tahun'] = now()->format('Y');//cur year
        $filterAbsensi = [];

        if($request->input('bulan',false)){
            $this->output['data']['bulan'] = $request->input('bulan');
            $this->output['data']['bulan'] = (int) ltrim($this->output['data']['bulan'],'0');
            if($this->output['data']['bulan']<=9)$this->output['data']['bulan'] = '0'.$this->output['data']['bulan'];
        }
        if($request->input('tahun',false)){
            $this->output['data']['tahun'] = $curYear = $request->input('tahun');
        }
        if($request->input('pegawaiId',false)){
            $this->output['data']['pegawaiId'] = $request->input('pegawaiId');
        }

        // jika bukan login pegawai
        if(Kepegawaian::CurOnline()==false){
            
            $filter = [];
            
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

            $this->output['data']['pegawai'] = Kepegawaian::listPegawai($filter);
            $this->output['data']['isPegawai'] = false;
            if($this->output['data']['pegawaiId']==0&&isset($this->output['data']['pegawai']['data'][0])){
                $this->output['data']['pegawaiId'] = $this->output['data']['pegawai']['data'][0]['id'];
            }
        }else{

            $this->output['data']['pegawaiId'] = Kepegawaian::CurOnline('id');
        }


        $filterAbsensi[] = ['pegawai_id',$this->output['data']['pegawaiId']];
        $filterAbsensi['bulan'] = $this->output['data']['tahun'].'-'.$this->output['data']['bulan'];
        
        $this->output['data']['absensi'] = Absensi::listAbsensi($filterAbsensi);
        //-------------------------------
        $newData = [];
        foreach ($this->output['data']['absensi']['data'] as $key => $value) {
            $newData[$value['tanggal']] = $value;
        }
        $this->output['data']['absensi']['data'] = $newData;

        $this->output['data']['daysInMonth'] = (new Carbon($filterAbsensi['bulan'].'-01'))->daysInMonth;
        //--------------------

        
        $curMonth = new Carbon($filterAbsensi['bulan'].'-01');
        $tglStart = $curMonth->format('Y-m-d'); 
        $tglEnd = $curMonth->format('Y-m-').$this->output['data']['daysInMonth'];
        $this->output['data']['rekapAbsensi'] = Absensi::rekapAbsensi(
            [$this->output['data']['pegawaiId']], $tglStart, $tglEnd
        );
        $this->output['data']['rekapAbsensi'] = 
            isset($this->output['data']['rekapAbsensi'][$this->output['data']['pegawaiId']])?
            $this->output['data']['rekapAbsensi'][$this->output['data']['pegawaiId']]:
            [];
        // dd($this->output['data']['rekapAbsensi']['jenisijin']);
        //-----------------------


        
        $jenisIjin = Master::listJenisIjin();
        $this->output['data']['jenisIjin'] = [];
        $this->output['data']['jenisIjinKategori'] = [0=>['jenisIjin'=>[],'kategori'=>[]]];
        foreach($jenisIjin['data'] as $val) {
            $this->output['data']['jenisIjin'][$val['id']] = $val;
            if(!isset($this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']]))
                $this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']] = [
                    'jenisIjin'=>[],
                    'kategori'=>$val['kategori']
                ];
            $this->output['data']['jenisIjinKategori'][$val['jenis_ijin_kategori_id']]['jenisIjin'][$val['id']] = $val;
        }
        

        // $hariLibur = Master::listHariLibur([
        //     'start' => $filter['tanggal_start'],
        //     'end' => $filter['tanggal_end']
        // ]);
        $hariLibur = Master::listHariLibur();
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

        return $this->done();
    }
}
