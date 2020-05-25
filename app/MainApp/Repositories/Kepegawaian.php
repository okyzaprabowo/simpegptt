<?php

namespace App\MainApp\Repositories;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
use App\MainApp\Models\Instansi;
use App\MainApp\Models\JenisIjin;
use App\MainApp\Models\Jabatan;
use App\MainApp\Models\Pegawai;
use App\MainApp\Models\PegawaiAlamat;
use App\MainApp\Models\PegawaiKeluarga;
use App\MainApp\Models\PegawaiPendidikan;
use App\MainApp\Models\PegawaiDoktah;

use Carbon\Carbon;
use App\Base\BaseRepository;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

class Kepegawaian extends BaseRepository
{
    
    protected $status_kawin = [];
     
    /**
     *  Pegawai
     * ------------------------------------------------------------
     */
    protected $dataPegawai = null;

    /**
     * general list pegawai
     */
    public function listPegawai($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        return $this->listPegawaiWithModel(
            Pegawai::with(['jabatan','agama','statusKawin','instansi']), 
            $filter,$offset, $limit,$orderBy);
    }

    /**
     * list pegawai dengan basensi
     */
    public function listPegawaiAbsensi($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $with = ['jabatan','agama','statusKawin','instansi'];

        if(isset($filter['tanggal_start'])){
            if(!isset($filter['tanggal_end'])){
                $tglEnd = new Carbon($filter['tanggal_end']);
                $lastDate = $tglEnd->daysInMonth;
                $filter['tanggal_end'] = $tglEnd->format('Y-m-').$lastDate;
            }
            $with['absensi'] = function($q) use($filter) { 
                $q->where('absensi.tanggal','>=',$filter['tanggal_start'])->where('absensi.tanggal','<=',$filter['tanggal_end']);
            };
            unset($filter['tanggal_start'],$filter['tanggal_end']);
        }else{
            $with[] = 'absensi';
        }
        $with[] = 'absensi.shift';
        $model = Pegawai::where('is_enable', 1)->with($with);
        return $this->listPegawaiWithModel($model, $filter,$offset, $limit,$orderBy);
    }

    public function listPegawaiWithModel($model=false, $filter = false,$offset = 0, $limit = 0 ,$orderBy=false) 
    {
        $filter['searchField'] = ['nama','kode','ktp','npwp'];
        
        if(isset($filter['instansi_id'])){
            $model = $model->where('instansi_id',$filter['instansi_id']);
            unset($filter['instansi_id']);
        }

        if(isset($filter['q'])){
            $model = $model->where(function($query) use ($filter) {
                foreach ($filter['searchField'] as $value) {
                    $query = $query->orWhere($value, 'LIKE', '%' . $filter['q'] . '%');
                }
                $query = $query->orWhereHas('jabatan', function($query) use ($filter) {
                    $query = $query->where('nama', 'LIKE', '%' . $filter['q'] . '%');

                });
            });
            unset($filter['q']);
        }

        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataPegawai = $this->pagination;
        return $this->dataPegawai;
    }

    public function getPaginationPegawai($path = '')
    {
        if(!isset($this->dataPegawai))$this->dataPegawai = $this->pagination;
        return $this->_getPagination($path, $this->dataPegawai);
    }

    public function getPegawai($filter = false) 
    {
        $model = Pegawai::with(['jabatan','agama','statusKawin','instansi','alamat','keluarga','pendidikan','user','doktah']);
        return $this->_getOne($model,$filter);
    }
    
    public function createPegawai($data) 
    {
        $model = new Pegawai;

        $input = $this->_filterAllowField($data, $model->getFillable());

        $input = $this->_filterPegawai($input);

        if(isset($input['instansi_id'])){
            if($instansi = Instansi::where('id',$input['instansi_id'])->first()){
                $input['instansi_induk_path'] = $instansi->induk_path;
            }            
        }

        $pegawai = $this->_create($model,$input);
        if($pegawai==false)return false;        
        
        if(isset($data['pendidikan'])){

            $modelPendidikan = new PegawaiPendidikan;
            foreach ($data['pendidikan'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelPendidikan->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];        
                $modelPendidikan->insert($tmpValue);
            }
        }

        if(isset($data['alamat'])){

            $modelAlamat = new PegawaiAlamat();
            foreach ($data['alamat'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelAlamat->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];             
                $modelAlamat->insert($tmpValue);
            }
        }

        if(isset($data['keluarga'])){

            $modelKeluarga = new PegawaiKeluarga();
            foreach ($data['keluarga'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelKeluarga->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];           
                $modelKeluarga->insert($tmpValue);
            }        
        }
        if(isset($data['doktah'])){

            $modelDoktah = new PegawaiDoktah();
            foreach ($data['doktah'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelDoktah->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];  
                
                if(isset($tmpValue['filepath']) && $tmpValue['filepath']){                
                    $tmpValue['filename'] = $tmpValue['filepath']->getClientOriginalName();
                    $tmpValue['filepath'] = $tmpValue['filepath']->store('doktah/'.$tmpValue['pegawai_id']);  
                    $modelDoktah->insert($tmpValue);   
                }

            }        
        }
        return $pegawai;
    }

    public function updatePegawai($where, $data) 
    {
        $model = new Pegawai;
        
        $pegawai = $this->_getOne($model, $where);
        
        if(!$pegawai){
            $this->error = "Pegawai tidak ditemukan";
            return false;
        }

        if(isset($data['pendidikan'])){

            $ids = [];
            $modelPendidikan = new PegawaiPendidikan;
            foreach ($data['pendidikan'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelPendidikan->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];
                if(array_key_exists('tanggal_masuk',$tmpValue) && empty($tmpValue['tanggal_masuk'])){
                    $tmpValue['tanggal_masuk'] = null;
                }
                if($value['id']!=0){
                    $this->_update($modelPendidikan, [['id',$value['id']]], $tmpValue);
                }else{                
                    $getId = $modelPendidikan->insert($tmpValue);
                    $value['id'] = $getId['id'];
                }
                $ids[] = $value['id'];
            }
            if(count($ids)>0)
                $modelPendidikan->where('pegawai_id',$pegawai['id'])->whereNotIn('id',$ids)->delete();

            unset($data['pendidikan']);
        }

        if(isset($data['alamat'])){

            $ids = [];
            $modelAlamat = new PegawaiAlamat();
            foreach ($data['alamat'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelAlamat->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];
                if($value['id']!=0){
                    $this->_update($modelAlamat, [['id',$value['id']]], $tmpValue);
                }else{                
                    $getId = $modelAlamat->insert($tmpValue);
                    $value['id'] = $getId['id'];
                }
                $ids[] = $value['id'];
            }
            if(count($ids)>0)
                $modelAlamat->where('pegawai_id',$pegawai['id'])->whereNotIn('id',$ids)->delete();

            unset($data['alamat']);
        }

        if(isset($data['keluarga'])){

            $ids = [];
            $modelKeluarga = new PegawaiKeluarga();
            foreach ($data['keluarga'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelKeluarga->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];
                if($value['id']!=0){
                    $this->_update($modelKeluarga, [['id',$value['id']]], $tmpValue);
                }else{                
                    $getId = $modelKeluarga->insert($tmpValue);
                    $value['id'] = $getId['id'];
                }
                $ids[] = $value['id'];
            }
            if(count($ids)>0)
                $modelKeluarga->where('pegawai_id',$pegawai['id'])->whereNotIn('id',$ids)->delete();

            unset($data['keluarga']);            
        }

        if(isset($data['doktah'])){

            // $fileName = $inputFile->getClientOriginalName();
            // $filePath = $inputFile->store('absensi');
            // if($filePath){
            //     return AbsensiRawUpload::create([
            //         'nama'=>$fileName,
            //         'mesin_absensi_id' => $mesinAbsenId,
            //         'file' => $filePath,
            //         'status' => 0,
            //         'is_from_file' => 1
            //     ]);
            // }

            $ids = [];
            $modelDoktah = new PegawaiDoktah();
            foreach ($data['doktah'] as $value) {
                $tmpValue = $this->_filterAllowField($value, $modelDoktah->getFillable());
                $tmpValue['pegawai_id'] = $pegawai['id'];
                $hasNewFile = false;
                
                if(isset($tmpValue['filepath']) && $tmpValue['filepath'] && !is_string($tmpValue['filepath'])){                
                    $tmpValue['filename'] = $tmpValue['filepath']->getClientOriginalName();
                    $tmpValue['filepath'] = $tmpValue['filepath']->store('doktah/'.$tmpValue['pegawai_id']); 
                    $hasNewFile = true;
                }else{
                    if(isset($tmpValue['filename']))unset($tmpValue['filename']);
                    if(isset($tmpValue['filepath']))unset($tmpValue['filepath']);
                }

                if($value['id']!=0){
                    //delete jika ada update file
                    if($hasNewFile){
                        $tmp = $modelDoktah->find($value['id']);
                        \Illuminate\Support\Facades\Storage::delete($tmp->filepath);
                    }

                    $this->_update($modelDoktah, [['id',$value['id']]], $tmpValue);
                }else{                
                    $getId = $modelDoktah->insert($tmpValue);
                    $value['id'] = $getId['id'];
                }
                $ids[] = $value['id'];
            }
            
            if(count($ids)>0){
                $resultDoktah = $modelDoktah->where('pegawai_id',$pegawai['id'])->whereNotIn('id',$ids)->get();
                foreach ($resultDoktah as $value) {
                    \Illuminate\Support\Facades\Storage::delete($value->filepath);
                }
                $modelDoktah->where('pegawai_id',$pegawai['id'])->whereNotIn('id',$ids)->delete();
            }
                

            unset($data['doktah']);            
        }

        $data = $this->_filterAllowField($data, $model->getFillable());
        $data = $this->_filterPegawai($data);
        
        //jika pindah instansi
        if(isset($data['instansi_id']) && $pegawai['instansi_id'] != $data['instansi_id']){
            if($instansi = Instansi::where('id',$data['instansi_id'])->first()){
                $data['instansi_induk_path'] = $instansi->induk_path;
            }            
        }

        return $this->_update($model,$where, $data);
    }

    private function _filterPegawai($data)
    {
        if(array_key_exists('tanggal_lahir',$data) && !$data['tanggal_lahir']){
            unset($data['tanggal_lahir']);
        }
        if(array_key_exists('tipe',$data) && !$data['tipe']){
            unset($data['tipe']);
        }
        return $data;
    }

    public function deletePegawai($id) 
    {
        $pegawai = $this->_getOne(new Pegawai,[['id',$id]]);
        
        if(!$pegawai){
            $this->error = 'Pegawai tidak ditemukan';
            return false;
        }
        $this->_delete(new Pegawai,[['id',$id]]);
        
        if($pegawai['foto']!=''){
            \Illuminate\Support\Facades\Storage::delete($pegawai['foto']);
        }

        $this->_delete(new PegawaiPendidikan,[['pegawai_id',$id]]);
        $this->_delete(new PegawaiAlamat,[['pegawai_id',$id]]);
        $this->_delete(new PegawaiKeluarga,[['pegawai_id',$id]]);
        $dataDoktah = PegawaiDoktah::where('pegawai_id',$id)->get();

        foreach ($dataDoktah as $value) {
            \Illuminate\Support\Facades\Storage::delete($value->filepath);
        }    
        $this->_delete(new PegawaiDoktah,[['pegawai_id',$id]]);    

        return true;
    }

    /**
     * Pegawai Yg online (user pemilik session saat ini)
     */

     public function CurOnline($field=false)
     {
         if(!isset($this->pegawaiOnline)){
            $this->pegawaiOnline = $this->getPegawai(['user_id',UserAuth::user('id')]);
         }
         if(!$this->pegawaiOnline)return false;
         if($field){
            return $this->pegawaiOnline[$field];
         }
         return $this->pegawaiOnline;
        
     }

}