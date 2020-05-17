<?php

namespace App\MainApp\Repositories;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
use App\MainApp\Models\JenisIjin;
use App\MainApp\Models\Jabatan;
use App\MainApp\Models\Pegawai;

use Carbon\Carbon;
use App\Base\BaseRepository;

class Laporan extends BaseRepository
{
    
     
    /**
     *  Pegawai
     * ------------------------------------------------------------
     */
    protected $dataPegawai = null;

    public function listPegawai($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) {
        $model = new Pegawai();
        $model = $model->with(['jabatan']);
        $filter['searchField'] = ['nama'];
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataPegawai = $this->pagination;
        return $this->dataPegawai;
    }

    public function getPaginationPegawai($path = '')
    {
        if(!isset($this->dataPegawai))$this->dataPegawai = $this->pagination;
        return $this->_getPagination($path, $this->dataPegawai);
    }

    public function getPegawai($filter = false) {
        $model = new Pegawai();
        return $this->_getOne($model,$filter);
    }
    
    public function createPegawai($data) {
        return $this->_create(new Pegawai,$data);
    }
    public function updatePegawai($where, $data) {
        return $this->_update(new Pegawai,$where, $data);
    }
    public function deletePegawai($id) {
        $this->_delete(new Pegawai,[['id',$id]]);
        return true;
    }


}