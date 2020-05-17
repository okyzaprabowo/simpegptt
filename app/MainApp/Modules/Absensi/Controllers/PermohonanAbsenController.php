<?php

namespace App\MainApp\Modules\Absensi\Controllers;

use Illuminate\Http\Request; 
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Repositories\Permohonan;
use Facades\App\MainApp\Models\PermohonanAbsen;
use Facades\App\MainApp\Models\Absensi;
use Facades\App\MainApp\Repositories\Kepegawaian;
use Illuminate\Support\Facades\Auth;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Carbon\Carbon;


use App\Base\BaseController;

class PermohonanAbsenController extends BaseController
{
    /**
     * GET - List data
     * 
     * @param Request $request
     *      orderBy
     *      orderType
     *      q
     *      offset
     *      limit
     */
    public function index(Request $request)
    {
        if(!UserAuth::hasAccess('Absensi.permohonan')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'permohonan_absen.list';
        $this->output['viewdata']['is_approval'] = false;
        $this->output['viewdata']['page_title'] = "Permohonan Ketidakhadiran";

        $orderBy = false;
        $filter = //untuk filter ke repo
        $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null)
            ];            

        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }else{
            $orderBy = ['waktu_mulai','DESC'];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }
        // dd($orderBy);
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 10);
       $pegawai =  Kepegawaian::getPegawai(['user_id',\UserAuth::user('id')]);
    //    dd($pegawai);
       if ($pegawai){
           $filter[] = ['pegawai_id',$pegawai['id']];

       }
    //    dd($filter)
        $this->output['data'] = Permohonan::listPermohonan(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );
        // var_dump($this->output['data']['data']);
        //generate pagination
        $this->output['viewdata']['pagination'] = Permohonan::getPaginationPermohonan(route('permohonan_absen.index', $paginationParams));
        $this->output['viewdata']['filter'] = $paginationParams;
        $this->output['viewdata']['ijin'] = Master::listJenisIjin();
        
        return $this->done();
    }

    public function approval(Request $request)
    {
        if(!UserAuth::hasAccess('Absensi.approval')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'permohonan_absen.listapproval';
        $this->output['viewdata']['is_approval'] = true;    
        $this->output['viewdata']['page_title'] = "Tindak Lanjut Permohonan Ketidakhadiran";
        $orderBy = false;
        $filter = //untuk filter ke repo
        $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null)
            ];

        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = 100;//$request->input('limit', 10);

        $filter[] = ['approve_status',0]; 

      //  if(\UserAuth::getActiveUserRoleCode()=='pejabat_approval'){
            $filter['instansi_id'] = \UserAuth::user('profile')['instansi_id'];
      //  }
        $this->output['data'] = Permohonan::listPermohonan(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        //generate pagination
        $this->output['viewdata']['pagination'] = Permohonan::getPaginationPermohonan(route('permohonan_absen.approval', $paginationParams));
        // $this->output['viewdata']['filter'] = $paginationParams;
        return $this->done();
    }

    /**
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'permohonan_absen.form';
        
        return $this->done();
    }

    /**
     * POST - save data baru
     */
    public function create(Request $request)
    {
        $this->response = redirect()->route('permohonan_absen.index');

        $data = $request->all();
      
        if($data){
            $pegawai = Kepegawaian::getPegawai(['user_id',Auth::user()->user_id]);//;
            //cari info jenis_ijin utk validasi 
            $jenis_ijin = Master::getJenisIjin(['id',$data['ijin_id']]);

            $batasIjinBulan = $jenis_ijin['batas_ijin'];
            $batasIjinTahun = $jenis_ijin['batas_ijin_tahunan'];
            $tglPermohonan = new Carbon($request->waktu_mulai);
             
            $waktu_mulai = new Carbon($request->waktu_mulai);
            $waktu_selesai = new Carbon($request->waktu_selesai);

            if ($waktu_selesai->lessThan($waktu_mulai)){
                // $this->setError('jumlah absen : '.$jmlAbsensi);
                $this->setError('Tanggal selesai tidak boleh < dari tanggal mulai');
                return $this->done();
            }

            $jmlAbsensi = Absensi::whereBetween('tanggal',[$waktu_mulai->format('Y-m-d'), $waktu_selesai->format('Y-m-d')])
             ->where('pegawai_id',$pegawai['id'])->count();
            if ($jmlAbsensi==0){
                // $this->setError('jumlah absen : '.$jmlAbsensi);
                $this->setError('Data absensi untuk tanggal yang dipilih belum tersedia ');
                return $this->done();
            }

            $jmlPermohonan = PermohonanAbsen::where('pegawai_id',$pegawai['id'])
                ->where(function($query) use ($waktu_mulai, $waktu_selesai){
                    $query->wherebetween('waktu_mulai', [$waktu_mulai->format('Y-m-d'), $waktu_selesai->format('Y-m-d')])
                        ->orwherebetween('waktu_selesai', [$waktu_mulai->format('Y-m-d'), $waktu_selesai->format('Y-m-d')]); 
                })->count();
           if ($jmlPermohonan>0){
               // $this->setError('jumlah absen : '.$jmlAbsensi);
               $this->setError('Data permohonan untuk tanggal yang dipilih sudah ada ');
               return $this->done();
           }
            
            $selisihHari = $tglPermohonan->diffInDays(date('Y-m-d'));
            // if ($selisihHari>5){
            //     $this->setError('Selisih hari : '.$selisihHari.', maksimal pengajuan adalah 5 hari dari waktu yang dimohonkan!');
            //     return $this->done();
            // }
            $filterPermohonan = []; 
            //cari jumlah ijin dari pegawai dari table permohonan
            $filterPermohonan[] = ['pegawai_id',$pegawai['id']];
            $filterPermohonan[] = ['ijin_id',$data['ijin_id']];
            $historyPermohonan = Permohonan::listPermohonan($filterPermohonan);
            $ijinBulanIni = PermohonanAbsen::where($filterPermohonan)
                    ->whereMonth('tanggal',$tglPermohonan->format('m'))->count();
            $ijinTahunIni = PermohonanAbsen::where($filterPermohonan)
                    ->whereYear('tanggal',$tglPermohonan->format('Y'))->count();
            //  dd($historyPermohonan);die;
            $jmlPermohonan = $historyPermohonan['count'];
            // var_dump(Auth::user()->user_id);die;
            $data['pegawai_id']  = $pegawai['id'];
            $data['tanggal'] = date('Y-m-d');
         
            $data['waktu_mulai'] = $waktu_mulai->format('Y-m-d');
            $data['waktu_selesai'] = $waktu_selesai->format('Y-m-d');
            $validator = [
                'pegawai_id' => 'required',
                'ijin_id' => 'required'
            ];
            $validator = \Validator::make($data, $validator);
            if ($validator->fails()) {       
                $this->setError(__('validation.inputerror'),$validator->messages());
                return $this->done();
            }

            if ($ijinBulanIni>$batasIjinBulan){
                $this->setError('Batas Permohonan sudah melewati maksimal dalam bulan ini');
                return $this->done();
            }
            if ($ijinTahunIni>$batasIjinTahun){
                $this->setError('Batas Permohonan sudah melewati maksimal dalam tahun ini');
                return $this->done();
            }
            
            unset($data['is_periode']);
            unset($data['jenis_ijin']);
            unset($data['pegawai']);
            $this->output['message'] = 'Data berhasil disimpan';
            $this->output['data'] = Permohonan::createPermohonan($data);            
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('permohonan_absen.index');
        }
        
        return $this->done();

    }
    /**
     * GET - get 1 data atau memunculkan form edit
     */
    public function edit(Request $request)
    {
        $this->response = 'permohonan.form';
        
        $id = $request->route('id');

        $this->output['data'] = Permohonan::getPermohonan($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('permohonan_absen.index');
        }

        return $this->done();

    }
    /**
     * PUT - update edit data
     */
    public function update(Request $request)
    {
        $this->response = redirect()->route('permohonan_absen.index');
        
        $id = $request->route('id');
        $data = $request->all();
        unset($data['is_periode']);
        unset($data['jenis_ijin']);
        unset($data['pegawai']);
        $this->output['message'] = 'Data berhasil diupdate';
        $waktu_mulai = new Carbon($request->waktu_mulai);
        $waktu_selesai = new Carbon($request->waktu_selesai);
        $data['waktu_mulai'] = $waktu_mulai->format('Y-m-d');
        $data['waktu_selesai'] = $waktu_selesai->format('Y-m-d');
        $this->output['data'] = Permohonan::updatePermohonan(['id',$id],$data);  
        if(!$this->output['data']){
            $this->setError(Permohonan::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('permohonan.edit',['id'=>$id]);
        }        
        return $this->done();

    }
    /**
     * PUT - approve edit data
     */
    public function approve(Request $request)
    {
        $this->response = redirect()->route('permohonan_absen.approval');
        
        $id = $request->route('id');
        // $data = $request->all();
      //  unset($data['is_periode']);
    //   var_dump(Auth::user());
        $this->output['message'] = 'Data berhasil diupdate';
        $data['approve_status'] = $request->approve_status;
        $data['approve_desc'] = $request->approve_desc;
        $data['approve_at'] = Now();
        $data['approve_by'] = Auth::user()->user_id;
        $this->output['data'] = Permohonan::approvePermohonan(['id',$id],$data);  
        dd($this->output['data']);
        if(!$this->output['data']){
            $this->setError(Permohonan::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('permohonan_absen.approval');
        }        
        return $this->done();

    }

    /**
     * DELETE - delete data
     */
    public function delete(Request $request) 
    {        
        $this->response = redirect()->route('permohonan_absen.index');

        $id = $request->route('id');

        if(Permohonan::deletePermohonan($id)){
            $this->output['message'] = 'Data berhasil ';
        }else{
            $this->setError(Permohonan::error());
        }
        
        return $this->done();

    }
}