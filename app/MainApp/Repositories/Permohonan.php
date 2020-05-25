<?php

namespace App\MainApp\Repositories;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
use App\MainApp\Models\JenisIjin;
use App\MainApp\Models\Jabatan;
use App\MainApp\Models\Pegawai;
use App\MainApp\Models\PermohonanAbsen;
use App\MainApp\Models\Absensi;

use Carbon\Carbon;
use App\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class Permohonan extends BaseRepository
{

    /**
     *  Permohonan
     * ------------------------------------------------------------
     */
    protected $dataPermohonan = null;

    public function listPermohonan($filter = false, $offset = 0, $limit = 0, $orderBy = false)
    {
        $model = new PermohonanAbsen();
        // $pegawai = Pegawai::with(['jabaan');
        $model = $model->with(['pegawai','pegawai.jabatan','pegawai.instansi','jenisIjin']);
        // only is_enable true
        $model->whereHas('pegawai',function($q){
            $q->where('is_enable', 1);
        });
        $filter['searchField'] = ['pegawai.nama'];
        // var_dump($filter);
        if ( (isset($filter['instansi_id']))  ||  (isset($filter['q']))  ){
            $model = $model->whereHas('pegawai',function($q) use ($filter){
               if (isset($filter['instansi_id'])) {
                   $q->where('instansi_id',$filter['instansi_id']);//->orWhere('instansi_induk_path','LIKE','%;'.$filter['instansi_id'].';%');
               }
                if(isset($filter['q'])){
                    $q = $q->where('nama', 'LIKE', '%' . $filter['q'] . '%');
                }
            });
            unset($filter['instansi_id']);
            unset($filter['q']);
        }
        $this->_list($model, $filter, $offset, $limit, $orderBy);
        $this->dataPermohonan = $this->pagination;
        return $this->dataPermohonan;
    }

    public function getPaginationPermohonan($path = '')
    {
        if (!isset($this->dataPermohonan)) $this->dataPermohonan = $this->pagination;
        return $this->_getPagination($path, $this->dataPermohonan);
    }

    public function getPermohonan($filter = false)
    {
        $model = new PermohonanAbsen();
        $model = $model->with(['pegawai', 'jenisIjin']);
        return $this->_getOne($model, $filter);
    }

    public function createPermohonan($data)
    {
        return $this->_create(new PermohonanAbsen, $data);
    }
    public function updatePermohonan($where, $data)
    {
        DB::beginTransaction();
        $fail = false;
        try {
            $permohonanOld = $this->getPermohonan($where);
            $this->resetAbsensi($permohonanOld);
            $data['approve_status'] = 0;
            $data['approve_desc'] = null;
            $data['approve_at'] = null;
            $data['approve_by'] = null;

            $this->_update(new PermohonanAbsen, $where, $data);
            DB::commit();
        } catch (\Exception $e) {
            throw $e;
            // dd($e);
            $error_msg = $e->getMessage();
            DB::rollback();
            $fail = true;
        }
        return !$fail;
    }

    public function approvePermohonan($where, $input)
    {
        $data =  $this->_update(new PermohonanAbsen, $where, $input);
        $permohonan = $this->getPermohonan($where);
        //  dd($permohonan);
        if ($permohonan && $input['approve_status'] == "1") { //hanya jika disetujui
            $waktu_mulai = new Carbon($permohonan['waktu_mulai']);
            $waktu_selesai = new Carbon($permohonan['waktu_selesai']);
            $dataAbsensi = Absensi::whereBetween('tanggal', [$waktu_mulai->format('Y-m-d'), $waktu_selesai->format('Y-m-d')])
                ->where('pegawai_id', $permohonan['pegawai_id'])
                ->whereNotIn('status',[4,5,6])//kecualikan hari libur dan yg sudah ada permohonannya
                ->get();
            foreach ($dataAbsensi as $v) {
                Absensi::where('id',$v->id)
                    ->update([
                        'permohonan_id' => $permohonan['id'],
                        'jenis_ijin_id' => $permohonan['ijin_id'],
                        'keterangan' => $permohonan['keterangan'],
                        'total_jam' => $v->jam_kerja,
                        'status_old' => $v->status,
                        'status' => 4
                    ]);
            }
            return true;

        }

        // $model = new Absensi;
        // $model = $model->where('tanggal','BETWEEN',$filter['bulan'].'%');
        // dd($data);
        return false;
    }

    public function deletePermohonan($id)
    {
        DB::beginTransaction();
        $fail = false;
        try {
            $permohonan = $this->getPermohonan(['id', $id]);
            $this->resetAbsensi($permohonan);
            $this->_delete(new PermohonanAbsen, [['id', $id]]);
            DB::commit();
        } catch (\Exception $e) {
            throw $e;
            // dd($e);
            $error_msg = $e->getMessage();
            DB::rollback();
            $fail = true;
        }
        return !$fail;
    }

    public function resetAbsensi($permohonan)
    {
        // $waktu_mulai = new Carbon($permohonan['waktu_mulai']);
        // $waktu_selesai = new Carbon($permohonan['waktu_selesai']);
        // whereBetween('tanggal', [$waktu_mulai->format('Y-m-d'), $waktu_selesai->format('Y-m-d')])
        $data = Absensi::where('pegawai_id', $permohonan['pegawai_id'])->where('permohonan_id',$permohonan['id'])->get();
        foreach ($data as $value) {
            $total_jam = 0;
            if (($value->scan_masuk != null) && ($value->scan_keluar != null)) {
                $jamScanMasuk = new Carbon($value->scan_masuk);
                // $jamScanKeluar = new Carbon($update['scan_keluar']);
                $total_jam = $jamScanMasuk->diffInSeconds($value->scan_keluar, false);
            }

            Absensi::where('id', $value->id)->update([
                'permohonan_id' => 0,
                'jenis_ijin_id' => 0,
                'keterangan' => null,
                'total_jam' => $total_jam,
                'status' => DB::raw('`status_old`'),
            ]);
            // dd($sql);
        }
    }
}
