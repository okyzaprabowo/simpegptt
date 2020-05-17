<?php

namespace App\MainApp\Repositories;

use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
use App\MainApp\Models\JenisIjin;
use App\MainApp\Models\JenisIjinKategori;
use App\MainApp\Models\Jabatan;
use App\MainApp\Models\JabatanInstansi;
use App\MainApp\Models\HariLibur;
use App\MainApp\Models\MesinAbsen;
use App\MainApp\Models\Instansi;
use App\MainApp\Models\Shift;
use App\MainApp\Models\ShiftDetail;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Base\BaseRepository;

class Master extends BaseRepository
{
    
    /**
     *  Jenis Ijin
     * ------------------------------------------------------------
     */
    protected $dataJenisIjin = null;

    public function listJenisIjin($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model = JenisIjin::with(['kategori']);
        $filter['searchField'] = ['nama','deskripsi'];
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataJenisIjin = $this->pagination;
        return $this->dataJenisIjin;
    }

    public function getPaginationJenisIjin($path = '')
    {
        if(!isset($this->dataJenisIjin))$this->dataJenisIjin = $this->pagination;
        return $this->_getPagination($path, $this->dataJenisIjin);
    }

    public function getJenisIjin($filter = false) {
        $model = JenisIjin::with(['kategori']);
        return $this->_getOne($model,$filter);
    }
    
    public function createJenisIjin($data) {
        return $this->_create(new JenisIjin,$data);
    }
    public function updateJenisIjin($where, $data) {
        return $this->_update(new JenisIjin,$where, $data);
    }
    public function deleteJenisIjin($id) {
        $this->_delete(new JenisIjin,[['id',$id]]);
        return true;
    }
    /**
     *  Jenis ijin kategori
     * ------------------------------------------------------------
     */
    protected $dataJenisIjinKategori = null;

    public function listJenisIjinKategori($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model =  new JenisIjinKategori();
        $filter['searchField'] = ['nama','singkatan'];       
        
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataJenisIjinKategori = $this->pagination;
        return $this->dataJenisIjinKategori;
    }

    public function getPaginationJenisIjinKategori($path = '')
    {
        if(!isset($this->dataJenisIjinKategori))$this->dataJenisIjinKategori = $this->pagination;
        return $this->_getPagination($path, $this->dataJenisIjinKategori);
    }

    public function getJenisIjinKategori($filter = false) {
        $model = new JenisIjinKategori();
        return $this->_getOne($model,$filter);
    }
    
    public function createJenisIjinKategori($data) {
        return $this->_create(new JenisIjinKategori,$data);
    }
    public function updateJenisIjinKategori($where, $data) {
        return $this->_update(new JenisIjinKategori,$where, $data);
    }
    public function deleteJenisIjinKategori($id) {
        if($this->_exists(new JenisIjin,['jenis_ijin_kategori_id',$id])){
            $this->error = 'Kategori masih digunakan, silahkan ganti dahulu data yang mereferensi ke kategori ini.';
            return false;
        }
        $this->_delete(new JenisIjinKategori,[['id',$id]]);
        return true;
    }
   
    /**
     *  Jabatan Sakira
     * ------------------------------------------------------------
     */
    protected $dataJabatan = null;

    public function listJabatan($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model =  Jabatan::with(['shift','jabatanInstansi.instansi']);// Shift::with(['shiftDetail']); 
        $filter['searchField'] = ['nama','deskripsi'];
       
        
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataJabatan = $this->pagination;
        return $this->dataJabatan;
    }

    public function getPaginationJabatan($path = '')
    {
        if(!isset($this->dataJabatan))$this->dataJabatan = $this->pagination;
        return $this->_getPagination($path, $this->dataJabatan);
    }

    public function getJabatan($filter = false) {
        $model = Jabatan::with(['shift','jabatanInstansi.instansi']);
        return $this->_getOne($model,$filter);
    }
    
    public function createJabatan($input) {
        // return $this->_create(new Jabatan,$data);
        $items = $input['jabatan_instansi'];
        unset($input['jabatan_instansi']);
        $data = $this->_create(new Jabatan,$input);
        if (!$data) {
            $this->error = "Create Error";
            return false;
        }
        
        foreach ($items as $key => $value){
         //     var_dump($value);die;
            $tmp_value['instansi_id'] = $value['instansi_id'];
            $tmp_value['jabatan_id'] = $data['id'];;
            $model = new JabatanInstansi;
            $model->insert($tmp_value);

        }
        return $data;
    }
    public function updateJabatan($where, $input) {
        $items = $input['jabatan_instansi'];
        unset($input['jabatan_instansi']);
        unset($input['shift']);
        $model = new JabatanInstansi;
        $model->where('jabatan_id', $input['id'])->delete();
        $data =  $this->_update(new Jabatan,$where, $input);
        foreach ($items as $key => $value){
          
               $tmp_value['instansi_id'] = $value['instansi_id'];
               $tmp_value['jabatan_id'] = $input['id'];;
               $model = new JabatanInstansi;
           //    if ($value['id']=="0")
                    $model->insert($tmp_value);
            //    else
              //      $model->update(['id'=>$value['id']],$tmp_value);
   
           }
        return $data;
    }
    public function deleteJabatan($id) {
        $this->_delete(new Jabatan,[['id',$id]]);
        $this->_delete(new JabatanInstansi, ['jabatan_id', $id]);
        return true;
    }

    /**
     *  Instansi
     * ------------------------------------------------------------
     */
    protected $dataEselon = null;

    public function listInstansi($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model = new Instansi();
        $filter['searchField'] = ['nama'];
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataEselon = $this->pagination;
        return $this->dataEselon;
    }

    /**
     * @return array
     *      data
     *      count integer jumlah item nya
     */
    public function listInstansiPerParent() {
        $model = Instansi::orderBy('induk_path','ASC')->get();
        $hasil = [
            'data'=>[],'count'=>0
        ];
        foreach ($model as $value) {
            if(!isset($hasil['data'][$value['induk']]))$hasil['data'][$value['induk']]=[];
            $hasil['data'][$value['induk']][] = $value;
            $hasil['count']++;
        }
        return $hasil;
    }

    public function getInstansi($filter = false) {
        $model = new Instansi();
        return $this->_getOne($model,$filter);
    }
    
    public function createInstansi($data) {
        if(array_key_exists('induk_path',$data) && is_null($data['induk_path']))$data['induk_path'] = '';
        return $this->_create(new Instansi,$data);
    }

    public function updateInstansi($where, $data) {

        $oldData = $this->_getOne(new Instansi,$where);

        if(array_key_exists('induk_path',$data) && is_null($data['induk_path']))$data['induk_path'] = '';
        

        //jika dipindah parent maka pastikan seluruh childnya ikut berpindah
        if($oldData['induk'] != $data['induk']){

            $induk = $this->_getOne(new Instansi,['id',$data['induk']]);
            $data['induk_path'] = $induk['induk_path'].(empty($induk['induk_path'])?';':'').$induk['id'].';';

            Instansi::where('induk_path','LIKE','%;'.$oldData['id'].';%')->update([
                'induk_path' => DB::raw("REPLACE(`induk_path`,'".$oldData['induk_path']."','".$data['induk_path']."')")
            ]);
        }
        return $this->_update(new Instansi,$where, $data);
    }

    public function deleteInstansi($id) {
        $this->_delete(new Instansi,[['id',$id]]);
        $this->_delete(new Instansi,[['induk_path','LIKE','%;'.$id.';%']]);
        return true;
    }

    /**
     *  Mesin ABsen
     * ------------------------------------------------------------
     */
    protected $dataMesinAbsen = null;

    public function listMesinAbsen($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model = MesinAbsen::with(['instansi']);
        $filter['searchField'] = ['nama','deskripsi'];
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataMesinAbsen = $this->pagination;
        return $this->dataMesinAbsen;
    }

    public function getPaginationMesinAbsen($path = '')
    {
        if(!isset($this->dataMesinAbsen))$this->dataMesinAbsen = $this->pagination;
        return $this->_getPagination($path, $this->dataMesinAbsen);
    }

    public function getMesinAbsen($filter = false) {
        $model = MesinAbsen::with(['instansi']);
        return $this->_getOne($model,$filter);
    }
    
    public function createMesinAbsen($data) {
        if(array_key_exists('instansi_id',$data)){
            if(!$data['instansi_id']){
                $this->error = 'Instansi belum dipilih';
                return false;
            }
        }else{
            $this->error = 'Instansi belum dipilih';
            return false;
        }
        return $this->_create(new MesinAbsen,$data);
    }
    public function updateMesinAbsen($where, $data) {
        if(array_key_exists('instansi_id',$data)){
            if(!$data['instansi_id']){
                $this->error = 'Instansi belum dipilih';
                return false;
            }
        }
        return $this->_update(new MesinAbsen,$where, $data);
    }
    public function deleteMesinAbsen($id) {
        $this->_delete(new MesinAbsen,[['id',$id]]);
        return true;
    }


    /**
     *  Hari Libur
     * ------------------------------------------------------------
     */
    protected $dataHariLibur = null;

    public function listHariLibur($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model = new HariLibur();
        $filter['searchField'] = ['nama'];
        if(isset($filter['start'])){
            $model = $model->where(function($q) use($filter) {
                $q->where('start','>=',$filter['start'])->orWhere('end','>=',$filter['start']);
            });
            unset($filter['start']);
        }
        if(isset($filter['end'])){
            $model = $model->where(function($q) use($filter) {
                $q->where('start','<=',$filter['end'])->orWhere('end','<=',$filter['end']);
            });
            unset($filter['end']);
        }
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataHariLibur = $this->pagination;
        return $this->dataHariLibur;
    }

    public function getPaginationHariLibur($path = '')
    {
        if(!isset($this->dataHariLibur))$this->dataHariLibur = $this->pagination;
        return $this->_getPagination($path, $this->dataHariLibur);
    }

    public function getHariLibur($filter = false) {
        $model = new HariLibur();
        return $this->_getOne($model,$filter);
    }
    
    public function createHariLibur($data) {
        return $this->_create(new HariLibur,$data);
    }
    public function updateHariLibur($where, $data) {
        return $this->_update(new HariLibur,$where, $data);
    }
    public function deleteHariLibur($id) {
        $this->_delete(new HariLibur,[['id',$id]]);
        return true;
    }



    /**
     *  Shift Kerja
     * ------------------------------------------------------------
     */
    protected $dataShift = null;

    public function listShift($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model = new Shift();
        $filter['searchField'] = ['nama','keterangan'];
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataShift = $this->pagination;
        return $this->dataShift;
    }

    public function getPaginationShift($path = '')
    {
        if(!isset($this->dataShift))$this->dataShift = $this->pagination;
        return $this->_getPagination($path, $this->dataShift);
    }

    public function getShift($filter = false) {
        $model =  Shift::with(['shiftDetail']); 
        // $model = $model->with['shiftDetail'];
        return $this->_getOne($model,$filter);
    }
    
    public function createShift($data) {
        return $this->_create(new Shift,$data);
    }
   
    public function createShiftDetail($data) {
        return $this->_create(new ShiftDetail,$data);
    }
    public function updateShift($where, $data) {
        return $this->_update(new Shift,$where, $data);
    }
    public function updateShiftDetail($where, $data) {
        return $this->_update(new ShiftDetail,$where, $data);
    }
    public function deleteShift($id) {
        $this->_delete(new Shift,[['id',$id]]);
        return true;
    }

}