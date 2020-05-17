<?php

namespace App\MainApp\Repositories;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
use App\MainApp\Models\Pegawai;
use App\MainApp\Models\Absensi as MAbsensi;
use App\MainApp\Models\AbsensiRaw;
use App\MainApp\Models\AbsensiRawUpload;
use App\MainApp\Models\Shift;
use App\MainApp\Models\ShiftDetail;
use App\MainApp\Models\HariLibur;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use App\Base\BaseRepository;
use App\MainApp\Modules\moduser\Services\UserAuth;
use Illuminate\Support\Facades\Log;

class Absensi extends BaseRepository
{    
    /**
     * ABSENSI RAW UPLOAD
     * 
     * grup data per upload (bisa upload raw file atau auto get dari mesin)
     * table absensi_raw_upload untuk menyimpan data file upload raw atau upload auto via grab ke mesin
     * -------------------------------------------------
     */

    public function listAbsensiRawUpload($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) 
    {
        $model = AbsensiRawUpload::with(['mesinAbsen']);
        $filter['searchField'] = ['nama'];
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataAbsensiRawFile = $this->pagination;
        return $this->dataAbsensiRawFile;
    }
    
    public function getPaginationAbsensiRawUpload($path = '')
    {
        if(!isset($this->dataAbsensiRawFile))$this->dataAbsensiRawFile = $this->pagination;
        return $this->_getPagination($path, $this->dataAbsensiRawFile);
    }
    
    public function getAbsensiRawUpload($filter = false) {
        $model = AbsensiRawUpload::with(['mesinAbsen']);
        return $this->_getOne($model,$filter);
    }
    
    public function updateAbsensiRawUpload($where, $data) {
        return $this->_update(new AbsensiRawUpload,$where, $data);
    }

    /**
     * general insert ke table absensi_raw_upload
     */
    public function createAbsensiRawUpload($data) {
        return $this->_create(new AbsensiRawUpload,$data);
    }

    /**
     * upload file data raw
     * 
     * @param integer $mesinAbsenId
     * @param Request $inputFile request input file
     * 
     * @return false|object absensi raw upload record
     */
    public function uploadFileRaw($mesinAbsenId=0,$inputFile)
    {        
        $fileName = $inputFile->getClientOriginalName();
        $filePath = $inputFile->store('absensi');
        if($filePath){
            return AbsensiRawUpload::create([
                'nama'=>$fileName,
                'mesin_absensi_id' => $mesinAbsenId,
                'file' => $filePath,
                'status' => 0,
                'is_from_file' => 1
            ]);
        }
        return false;
    }

    /**
     * delete data
     */
    public function deleteAbsensiRawUpload($id) 
    {
        $data = AbsensiRawUpload::find($id);
        if(!$data) {
            $this->error = 'Data tidak ditemukan';
            return false;
        }
        //jika status sedang "proses" (baik proses insert atau proses kalkulasi) maka tolak
        if($data->status == 1 || $data->status == 3){
            $this->error = 'Data sedang dalam suatu proses, tunggu beberapa saat lagi';
            return false;
        }

        if($data->is_from_file)
            \Illuminate\Support\Facades\Storage::delete($data->file);
        AbsensiRaw::where('absensi_raw_upload_id',$id)->delete();
        $this->_delete(new AbsensiRawUpload,['id',$id]);

        return true;
    }
    

    /**
     * ANBSENSI RAW
     * 
     * data raw absensi
     * ------------------------------------------------------
     */

    public function listAbsensiRaw($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) 
    {
        $model = AbsensiRaw::with(['pegawai']);
        $filter['searchField'] = ['pin'];
        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataAbsensiRaw = $this->pagination;
        return $this->dataAbsensiRaw;
    }
    
    public function getPaginationAbsensiRaw($path = '')
    {
        if(!isset($this->dataAbsensiRaw))$this->dataAbsensiRaw = $this->pagination;
        return $this->_getPagination($path, $this->dataAbsensiRaw);
    }

    public function getAbsensiRaw($filter = false) {
        $model = new AbsensiRaw();
        return $this->_getOne($model,$filter);
    }
    
    public function createAbsensiRaw($data) {
        return $this->_create(new AbsensiRaw,$data);
    }

    public function updateAbsensiRaw($where, $data) {
        return $this->_update(new AbsensiRaw,$where, $data);
    }

    public function deleteAbsensiRaw($id) {
        $this->_delete(new AbsensiRaw,[['id',$id]]);
        return true;
    }

    /**
     * insert data file raw yang sudah diupload sebelumnya (absensi_raw_upload) ke absensi_raw
     */
    public function InsertFromFileRaw($absensiRawUploadId)
    {        
        ini_set('memory_limit','1024M');
        set_time_limit(0);

        $uploadId = AbsensiRawUpload::find($absensiRawUploadId);
        if(!$uploadId)return false;
        
        AbsensiRawUpload::where('id',$absensiRawUploadId)->update(['status'=>1]);

        $fullFilePath = \Illuminate\Support\Facades\Storage::path($uploadId->file);
        $fileData = file_get_contents($fullFilePath);
        $fileData = explode("\r\n", $fileData);
        $newData = [];
        $now = Now();
        $i = 0;
        foreach ($fileData as $value) {
            $tmp = explode("\t",trim($value));
            if(count($tmp)==6){
                $pegawai = Pegawai::where('kode',$tmp[0])->first();
                //jika pegawai ada
                if($pegawai){
                    $tmpRaw = AbsensiRaw::where('pin',$tmp[0])->where('scan_time',$tmp[1])->exists();
                    //jika tidak ada maka insert
                    if($tmpRaw==false){
                        $i++;
                        $newData[] = [
                            'absensi_raw_upload_id' => $uploadId->id,
                            'mesin_absensi_id' => $uploadId->mesin_absensi_id,
                            'absensi_id' => 0,//di isi jika data sudah diproses
                            'pin' => $tmp[0],
                            'scan_time' => $tmp[1],
                            'device_id' => $tmp[2],
                            'type' => $tmp[3],
                            'data_type' => $tmp[4],
                            'work_code' => $tmp[5],
                            'is_from_file' => $uploadId->is_from_file,
                            'status' => 0,
                            'created_at' => $now,
                            'updated_at' => $now
                        ];

                        //insert per 10 row
                        if($i>=10){
                            $i=0;
                            AbsensiRaw::insert($newData);
                            $newData = [];
                        }     
                    }
                }
            }
        }

        if(count($newData)>0)AbsensiRaw::insert($newData);

        AbsensiRawUpload::where('id',$absensiRawUploadId)->update(['status'=>2]);
        //delete file setelah selesai
        // \Illuminate\Support\Facades\Storage::delete($filePath);
    }
    
    /**
     * kalkulasi seluruh data absensi raw yang belum diproses
     */
    public function kalkulasiRawAbsensi()
    {
        ini_set('memory_limit','1024M');
        set_time_limit(0);
        
        $startTime = microtime(true);
        //ambil data file upload yg masih proses insert(1) dan proses kalkulasi (3)
        $absensiUpload = AbsensiRawUpload::whereIn('status',[1,3])->first();
        //jika ada berarti tolak
        if($absensiUpload){
            $this->error = 'Masih ada data raw yang sedang diproses';
            return false;
        }
        //ambil seluruh file upload yg sudah berstatus selesai insert (2)
        $rawUploads = $this->listAbsensiRawUpload([['status',2]]);

        //ubah statusnya jadi sedang proses kalkulasi (3)
        foreach ($rawUploads['data'] as $value) {
            $this->updateAbsensiRawUpload($value['id'],['status'=>3]);  
        }

        $data = [];//data
        $shift = [];//di save shift nya agar tidak get terus kalo udah pernah diget
        $absensiLoaded = [];//daftar absensi per bulang yang sudah digenerate pada proses ini, ditandai agar tidak digenerate ulang

        //Ambil semua data raw yang belum diproses, lalu grup kan per pegawai per tgl absen yang nantinya akan dikalkulasi ulang
        $dataRaw = AbsensiRaw::with(['pegawai','pegawai.jabatan'])->where('status',0)->orderBy('pin','ASC')->orderBy('scan_time','ASC')->get();
        Log::info('Starting kalkulasi absensi : '.$dataRaw->count().' Row');
        
        $i=0;
        $rawCount=0;
        //looping seluruh data raw yang belum diproses, grupkan ke absensi mana dia masuk
        foreach ($dataRaw as $value) {
            $val = $value->toArray();
            $tanggal = (new Carbon($val['scan_time']))->format('Y-m-d');
            $tanggalBulan = (new Carbon($val['scan_time']))->format('Y-m');            

            //ambil data absensi dari data raw ini masuk ke absen tgl berapa
            $curAbsensi = MAbsensi::where('pegawai_id',$val['pegawai']['id'])
                ->where('jam_masuk_mulai_scan','<=',$val['scan_time'])
                ->where('jam_keluar_akhir_scan','>=',$val['scan_time'])
                ->whereIn('status',[0,2,3,4])//hanya proses jika absens masih berstatus 0 (baru generate), 2 (alpa), 3 (kurang scan), 4 ijin/permohonan
                ->first();

            //jika absensi raw yang diproses tidak dalam rentang waktu data absensi yang ada,
            //maka bisa dipastikan data absensi di rentang waktu tersebut belum ada, 
            //maka generate dulu default absen 1 bulan di tgl bersangkutan
            if(!$curAbsensi && !isset($absensiLoaded[$val['pegawai']['id']][$tanggalBulan])){

                if(!isset($absensiLoaded[$val['pegawai']['id']]))$absensiLoaded[$val['pegawai']['id']] = [];
                $absensiLoaded[$val['pegawai']['id']][$tanggalBulan] = true;

                $shiftId = isset($val['pegawai']['jabatan']['shift_id'])?$val['pegawai']['jabatan']['shift_id']:0;

                $this->generateOneMonthAbsensi($val['pegawai']['id'],$val['scan_time'],$shiftId,true);  
                
                //setelah di generate, maka ambil data absensi nya
                $curAbsensi = MAbsensi::where('pegawai_id',$val['pegawai']['id'])
                    ->where('jam_masuk_mulai_scan','<=',$val['scan_time'])
                    ->where('jam_keluar_akhir_scan','>=',$val['scan_time'])
                    ->whereIn('status',[0,2,3,4])//hanya proses jika absens masih berstatus 0 (baru generate), 2 (alpa), 3 (kurang scan), 4 ijin/permohonan
                    ->first(); 
                    
            }

            if(!isset($data[$val['pegawai']['id']]))
                $data[$val['pegawai']['id']]=[];

            if(!isset($data[$val['pegawai']['id']][$tanggal]))
                $data[$val['pegawai']['id']][$tanggal]=[
                    'pegawai' => $val['pegawai'],
                    'tanggal' => $tanggal,
                    'data' => false,//data record table absensi
                    'ids'=>[],//id-id data raw yang masuk di tanggal ini
                    'noIds'=>[] // id-id absensi raw yang waktu scan nya diluar waktu wajar
                ];
                
            //jika absensi ada maka tandai pegawai dan tanggal mana saja yang akan dikalkulasi dan kalkulasi ulang absensi nya
            if($curAbsensi){
                //jika data absensi belum disimpan, maka simpan
                if($data[$val['pegawai']['id']][$tanggal]['data']==false)
                    $data[$val['pegawai']['id']][$tanggal]['data'] = $curAbsensi->toArray();

                $data[$val['pegawai']['id']][$tanggal]['ids'][] = $val['id'];

            //jika absensi tidak ada berarti waktu scan nya diluar waktu wajar (waktu wajar adalah 3 jam sebelum dan sesudah jam masuk ; 3 jam sebelum dan setelah jam keluar)
            }else{                
                $data[$val['pegawai']['id']][$tanggal]['noIds'][] = $val['id'];
            }

            $i++;            
			if($i>=4){
				$i=0;
				usleep(1000);
            }
            $rawCount++;
            //log tiap 15 detik
            if((microtime(true)-$startTime)>=20){
                $startTime = microtime(true);
                Log::info('looping data on progress : '.$rawCount.' row');
            }
        }

        Log::info('Total pegawai dari data absen : '.count($data).' orang');
        Log::info('looping seluruh data raw berhasil, mulai proses kalkulasi...');
        
        $rawCount = 0;
        //mulai kalkulasi
        foreach ($data as $pegawaiId => $pegawai) {
            foreach ($pegawai as $tanggal => $absen) {

                //jika ids kosong atau data absen tidak ada berarti tidak ada data absen yang layak dikalkulasi, 
                //maka cukup update status & absensi_id data raw yang tidak layaknya saja tanpa perlu kalkulasi ulang
                if(!$absen['data'] || count($absen['ids']) <= 0){
                    if($absen['data']){
                        $id = $absen['data']['id'];
                    }else{
                        if($tmpAbsen = MAbsensi::where('pegawai_id',$pegawaiId)->where('tanggal',$tanggal)->first()){
                            $id = $tmpAbsen->id;
                        }else{
                            //ada error yang ga tau apa, jadi skip weh
                            continue;
                        }
                    }                 
                    AbsensiRaw::whereIn('id',$absen['noIds'])->update([
                        'absensi_id'=>$id,
                        'status'=>1
                    ]);
                    continue;
                }

                //ambil seluruh absensi raw yang termasuk dalam absen ini
                $dataRaw = AbsensiRaw::with(['pegawai','pegawai.jabatan'])
                    ->where('pin',$absen['pegawai']['kode'])
                    ->where('scan_time','>=',$absen['data']['jam_masuk_mulai_scan'])
                    ->where('scan_time','<=',$absen['data']['jam_keluar_akhir_scan'])
                    ->orderBy('scan_time','ASC')->get();

                $update = [
                    'scan_masuk'=>null,
                    'scan_keluar'=>null
                ];  

                $ids = [];

                //dari absen raw diatas, tentukan scan masuk dan scan keluarnya (yang pertama dan yang terakhir)
                foreach ($dataRaw as $value) {
                    $ids[] = $value->id;
                    if($update['scan_masuk']==null && (
                        $value->scan_time >= $absen['data']['jam_masuk_mulai_scan'] &&
                        $value->scan_time <= $absen['data']['jam_masuk_akhir_scan'] 
                    )){
                        $update['scan_masuk'] = $value->scan_time;
                        continue;
                    }

                    if($update['scan_keluar']==null && (
                        $value->scan_time >= $absen['data']['jam_keluar_mulai_scan'] &&
                        $value->scan_time <= $absen['data']['jam_keluar_akhir_scan'] 
                    )){
                        $update['scan_keluar'] = $value->scan_time;                     
                    }
                }

                $absen['data']['scan_masuk'] = $update['scan_masuk'];
                $absen['data']['scan_keluar'] = $update['scan_keluar'];

                $update = $this->kalkulasiAbsensi($absen['data']);

                if($update == false)continue;

                MAbsensi::where('id',$absen['data']['id'])
                    ->update($update);
                
                //tandai semua absen raw
                AbsensiRaw::whereIn('id',$ids)->update([
                    'absensi_id'=>$absen['data']['id'],
                    'status'=>1
                ]);

                //tandai semua absen raw
                if(count($absen['ids']) > 0){
                    AbsensiRaw::whereIn('id',$absen['ids'])->update([
                        'absensi_id'=>$absen['data']['id'],
                        'status'=>1
                    ]);
                }

                //tandai semua absen raw
                if(count($absen['noIds']) > 0){
                    AbsensiRaw::whereIn('id',$absen['noIds'])->update([
                        'absensi_id'=>$absen['data']['id'],
                        'status'=>1
                    ]);
                }
                $rawCount++;
                //log tiap 15 detik
                if((microtime(true)-$startTime)>=20){
                    $startTime = microtime(true);
                    Log::info('kalkulasi on progress : '.$rawCount.' row');
                }
            }
            
			usleep(1000);
        }
        
        Log::info('Kalkulasi absen selesai...ubah status file upload menjadi selesai.');
        
        foreach ($rawUploads['data'] as $value) {
            $this->updateAbsensiRawUpload($value['id'],['status'=>4]);  
        }
    }

    /**
     * kalkulasi per 1 recored absensi yg masih berstatus 0
     * @param $forceUpdate true jika tidak mendetek status absensi
     */
    public function kalkulasiAbsensi($absen,$autoUpdate=false,$forceUpdate=false)
    {
        if(!is_array($absen))$absen = MAbsensi::find($absen)->toArray();

        //hanya proses jika absens masih berstatus 0 (baru generate), 2 (alpa), 3 (kurang scan)
        //kecuali force update
        if(!$forceUpdate)
            if(!in_array($absen['status'],[0,2,3])){
                return false;
            }

        //hanya kalkulasi yg tanggal nya sudah terlewat
        if($absen['tanggal'] > now()->format('Y-m-d')){
            return false;
        }

        $update = [
            'scan_masuk' => $absen['scan_masuk'],
            'scan_keluar' => $absen['scan_keluar'],
            'kelebihan_jam' => 0,
            'keterlambatan_jam' => 0,
            'pulang_cepat_jam' => 0,
            'jam_kerja' => 0,
            'total_jam' => 0,
            'kekurangan_jam' => 0,
            'status' => $absen['status'],
        ];  

        // jika tidak ada jam masuk maka set libur

        if(empty($absen['jam_masuk']) || empty($absen['jam_keluar'])){
            return $this->kalkulasiAbsensi_cekLibur($absen,$update,$autoUpdate);
        }

        $jamKerjaMasuk = new Carbon($absen['jam_masuk']);
        $jamKerjaKeluar = new Carbon($absen['jam_keluar']);

        //total seharusnya jam kerja
        $update['jam_kerja'] = $jamKerjaMasuk->diffInSeconds($absen['jam_keluar'], false);
                        
        /**
         * proses hanya jika scan masuk dan keluarnya ada
         */                
        if($absen['scan_masuk']!=null && $absen['scan_keluar']!=null){                   

            $jamScanMasuk = new Carbon($absen['scan_masuk']);
            $jamScanKeluar = new Carbon($absen['scan_keluar']);                     

            //total realisasi jam kerja hari ini, dari scan masuk ke scan keluar
            $update['total_jam'] = $jamScanMasuk->diffInSeconds($absen['scan_keluar'], false);

            //jika jam scan masuk melebihi jam masuk maka telat
            if($jamScanMasuk->greaterThan($jamKerjaMasuk))
                $update['keterlambatan_jam'] = $jamKerjaMasuk->diffInSeconds($absen['scan_masuk'], false);

            //dari scan keluar ke jam keluar
            if($jamScanKeluar->lessThan($jamKerjaKeluar))
                $update['pulang_cepat_jam'] = $jamScanKeluar->diffInSeconds($absen['jam_keluar'], false);

            //kekurangan jam kerja, dari total jam kerja ke total seharusnya kerja
            if($update['jam_kerja']>$update['total_jam']){
                $update['kekurangan_jam'] = $update['jam_kerja']-$update['total_jam'];

            //kelebihan jam kerja, dari total masuk ke total jam kerja
            }else if($update['total_jam']>$update['jam_kerja']){
                $update['kelebihan_jam'] = $update['total_jam']-$update['jam_kerja'];                        
            }
              
            //telat dan kurang jam dianggp hadir
            if($absen['status']!=4)$update['status'] = 1;//hadir/telat (kurang jam)            

        }else if($absen['status']!=4){

            //jika kosong 2 nya nya anggap alpa
            if($absen['scan_masuk']==null && $absen['scan_keluar']==null){ 
                $update['status'] = 2;
            //jika hanya salah satunya yg kosong maka berarti kurang scan
            }else{  
                $update['status'] = 3;//kurang scan : scan_masuk atau scan_keluar ada yg null
            }
        }
        
        if($autoUpdate){
            MAbsensi::where('id',$absen['id'])
                ->update($update);
        }

        return $update;
    }

    private function kalkulasiAbsensi_cekLibur($absen,$update,$autoUpdate)
    {

        //jika status belum libur maka ubah jadi libur
        if(!($update['status']==5 || $update['status']==6)){
            
            $weekMap = [
                0 => 'minggu',
                1 => 'senin',
                2 => 'selasa',
                3 => 'rabu',
                4 => 'kamis',
                5 => 'jumat',
                6 => 'sabtu',
            ];

            //cek apakah libur shift
            $dataShift = ShiftDetail::where('shift_id',$absen['shift_id'])->where(function($q) use($absen) {
                $q->whereNull('range_awal')->orWhere(function($q) use($absen) {
                    $q->where('range_awal','<=',$absen['tanggal'])->where('range_akhir','>=',$absen['tanggal']);
                });
            });

            //jika record ada 2 berarti sedang puasa, ambil yg tidak null
            if($dataShift->count()>=2){
                $dataShift = $dataShift->whereNotNull('range_awal')->first()->toArray();
            }else{
                $dataShift = $dataShift->first()->toArray();
            }

            $tgl = new Carbon($absen['tanggal']);    
            $day = $tgl->dayOfWeek;//hari dalam minggu ini 0-6

            //jika empty maka libur shift
            if(empty($dataShift[$weekMap[$day].'_masuk'])){
                $update['status']=5;
                //jika bukan maka cek apakah libur hari libur
            }else{
                //jika hari libur dari config hari libur
                if(HariLibur::where('start','<=',$absen['tanggal'])->where('end','>=',$absen['tanggal'])->exists()){
                    $update['status']=5;
                }else{                        
                    $update['status']=6;
                }
            }

        }

        if($autoUpdate){
            MAbsensi::where('id',$absen['id'])
                ->update($update);
        }

        return $update;
    }

    /**
     * ANBSENSI RAW
     * 
     * data raw absensi
     * ------------------------------------------------------
     */

    /**
     * Get rekap absensi berdasarkan pegawai dan tanggal
     * 
     * @return array per pegawai
     */
    public function rekapAbsensi($pegawaiIds,$tanggalStart,$tanggalEnd)
    {

        $now = now()->format('Y-m-d');
        $model = MAbsensi::with(['pegawai','jenisIjin.kategori'])->where('tanggal','>=',$tanggalStart)->where('tanggal','<=',$tanggalEnd);
        $model = $model->whereHas('pegawai',function($q) use ($pegawaiIds) {
            $q->whereIn('id',$pegawaiIds);
        });
        
        $pegawai = $model->get();

        $hasil = [];
        foreach($pegawai as $value) {
            $val = $value->toArray();
            if(!isset($hasil[$val['pegawai_id']]))$hasil[$val['pegawai_id']]=[];

            /**
             * init var
             * --------------
             */
            //init total jumlah hari kerja
            if(!isset($hasil[$val['pegawai_id']]['kerja']))
                $hasil[$val['pegawai_id']]['kerja'] = 0;//jumlah hari

            //init jumlah hari libur
            if(!isset($hasil[$val['pegawai_id']]['libur']))
                $hasil[$val['pegawai_id']]['libur'] = 0;//jumlah hari

            //init jumlah hadir
            if(!isset($hasil[$val['pegawai_id']]['hadir']))
                $hasil[$val['pegawai_id']]['hadir'] = 0;//jumlah hari

            //init jumlah alpa
            if(!isset($hasil[$val['pegawai_id']]['alpa']))
                $hasil[$val['pegawai_id']]['alpa'] = 0;//jumlah hari

            //init jumlah melakukan permohonan
            if(!isset($hasil[$val['pegawai_id']]['ijin']))
                $hasil[$val['pegawai_id']]['ijin'] = 0;//jumlah hari

            //init jumlah absen yang tidak lengkap
            if(!isset($hasil[$val['pegawai_id']]['tidaklengkap']))
                $hasil[$val['pegawai_id']]['tidaklengkap'] = 0;//jumlah hari
            

            //init jumlah masuk telat
            if(!isset($hasil[$val['pegawai_id']]['telat'])){
                $hasil[$val['pegawai_id']]['telat'] = 0;//jumlah hari
                $hasil[$val['pegawai_id']]['telat_jam'] = 0;//jumlah detik
            }

            //init jumlah pulang cepat
            if(!isset($hasil[$val['pegawai_id']]['cepat'])){
                $hasil[$val['pegawai_id']]['cepat'] = 0;//jumlah hari
                $hasil[$val['pegawai_id']]['cepat_jam'] = 0;//jumlah detik
            }
            
            //init jumlah kelebihan jam
            if(!isset($hasil[$val['pegawai_id']]['kelebihan_jam']))
                $hasil[$val['pegawai_id']]['kelebihan_jam'] = 0;//jumlah detik

            //init jumlah kelebihan jam
            if(!isset($hasil[$val['pegawai_id']]['kekurangan_jam']))
                $hasil[$val['pegawai_id']]['kekurangan_jam'] = 0;//jumlah detik
            
            
            //init jumlah jam kerja seharusnya
            if(!isset($hasil[$val['pegawai_id']]['jam_kerja']))
                $hasil[$val['pegawai_id']]['jam_kerja'] = 0;//jumlah detik

            //init jumlah total jam kerja
            if(!isset($hasil[$val['pegawai_id']]['total_jam']))
                $hasil[$val['pegawai_id']]['total_jam'] = 0;//jumlah detik
                
            /**
             * ----------------------------
             */

            //jika tanggal lebih dari hari ini maka tidak usah dikalkulasi
            if($val['tanggal'] > $now)
                continue;
            
            //jika libur
            if($val['status']==5 || $val['status']==6){
                $hasil[$val['pegawai_id']]['libur']++;
            }else{
                $hasil[$val['pegawai_id']]['kerja']++;
            
                switch ($val['status']) {
                    case 1://hadir / masuk
                        $hasil[$val['pegawai_id']]['hadir']++;
                        break;      
                    case 2://alpa
                        $hasil[$val['pegawai_id']]['alpa']++;
                        break;      
                    case 3://absen tidak lengkap
                        $hasil[$val['pegawai_id']]['tidaklengkap']++;
                        break;     
                    case 4://ijin
                        $hasil[$val['pegawai_id']]['ijin']++;
                        break;
                    //jika ga ada status anggap alpha               
                    default:
                        $hasil[$val['pegawai_id']]['alpa']++;
                        break;
                }
            }

            if($val['keterlambatan_jam']){
                $hasil[$val['pegawai_id']]['telat']++;
                $hasil[$val['pegawai_id']]['telat_jam'] = $hasil[$val['pegawai_id']]['telat_jam']+$val['keterlambatan_jam'];
            }

            if($val['pulang_cepat_jam']){
                $hasil[$val['pegawai_id']]['cepat']++;
                $hasil[$val['pegawai_id']]['cepat_jam'] = $hasil[$val['pegawai_id']]['cepat_jam']+$val['pulang_cepat_jam'];
            }
            
            if($val['kelebihan_jam']){
                $hasil[$val['pegawai_id']]['kelebihan_jam'] = $hasil[$val['pegawai_id']]['kelebihan_jam']+$val['kelebihan_jam'];
            }

            if($val['kekurangan_jam']){
                $hasil[$val['pegawai_id']]['kekurangan_jam'] = $hasil[$val['pegawai_id']]['kekurangan_jam']+$val['kekurangan_jam'];
            }

            //jika jam kerja 0 tapi jam masuk dan jam keluar ada maka kalkulasi ulang dan save
            if($val['jam_kerja']==0 && $val['jam_masuk'] && $val['jam_keluar']){
                
                $jamKerjaMasuk = new Carbon($val['jam_masuk']);
                $jamKerjaKeluar = new Carbon($val['jam_keluar']);

                //total seharusnya jam kerja
                $val['jam_kerja'] = $jamKerjaMasuk->diffInSeconds($val['jam_keluar'], false);
                MAbsensi::where('id',$val['id'])->update(['jam_kerja'=>$val['jam_kerja'] ]);
            }

            $hasil[$val['pegawai_id']]['jam_kerja'] = $hasil[$val['pegawai_id']]['jam_kerja']+$val['jam_kerja'];
                        
            if($val['total_jam']){
                $hasil[$val['pegawai_id']]['total_jam'] = $hasil[$val['pegawai_id']]['total_jam']+$val['total_jam'];
            }
            // dd($val['jenis_ijin']);
            //jenis ijin
            if($val['status']==4 && $val['jenis_ijin']){
                if(!isset($hasil[$val['pegawai_id']]['jenisijin']))
                    $hasil[$val['pegawai_id']]['jenisijin'] = [];

                if(!isset($hasil[$val['pegawai_id']]['jenisijin'][$val['jenis_ijin_id']]))
                    $hasil[$val['pegawai_id']]['jenisijin'][$val['jenis_ijin_id']] = 0;

                $hasil[$val['pegawai_id']]['jenisijin'][$val['jenis_ijin_id']] ++;

                if($val['jenis_ijin']['kategori']){
                    if(!isset($hasil[$val['pegawai_id']]['jenisIjinKategori']))
                        $hasil[$val['pegawai_id']]['jenisIjinKategori'] = [];
    
                    if(!isset($hasil[$val['pegawai_id']]['jenisIjinKategori'][$val['jenis_ijin']['jenis_ijin_kategori_id']]))
                        $hasil[$val['pegawai_id']]['jenisIjinKategori'][$val['jenis_ijin']['jenis_ijin_kategori_id']] = 0;
    
                    $hasil[$val['pegawai_id']]['jenisIjinKategori'][$val['jenis_ijin']['jenis_ijin_kategori_id']] ++;

                }
            }

        }
        return $hasil;
    }

    public function formatJamKerja($waktu)
    {
        $minus = '';
        if($waktu<0){
            $minus = '- ';
            $waktu = -$waktu;
        }
        $detik = $waktu%60;
        if($detik<=9)$detik = '0'.$detik;

        $menit = floor($waktu/60)%60;
        if($menit<=9)$menit = '0'.$menit;

        $jam = floor(floor($waktu/60)/60);
        if($jam<=9)$jam = '0'.$jam;

        return $minus.$jam.':'.$menit.':'.$detik;
    }

    /**
     * @param string $baseTime
     * @param integer $addTime detik penambahannya
     */
    public function addTime($baseTime='00:00:00',$addTime)
    {
        return 0;
        $dateTimeObject = new \DateTime('23:59:00');
        $modifiedTimeString = $dateTimeObject->modify('+10 minutes')->modify('+25 seconds')->format('H:i:s');
        dd($modifiedTimeString);
                // set first time
        $baseTime = Carbon::createFromFormat('H:i:s', $baseTime);
        $addTime = Carbon::createFromFormat('H:i:s', $addTime);
        // add time
        $resultTime = $baseTime->addHours($addTime->format('H'))->addMinutes($addTime->format('i'))->addSeconds($addTime->format('s'))->toTimeString();
        // dd($baseTime->diffInHours($resultTime));
        // get hours different
        return $baseTime->diffInMinutes($resultTime);
    }

    public function listAbsensi($filter = false,$offset = 0, $limit = 0 ,$orderBy=false) 
    {
        $model = MAbsensi::with(['pegawai']);
        $filter['searchField'] = ['pin'];

        if(isset($filter['bulan'])){
            $model = $model->where('tanggal','LIKE',$filter['bulan'].'%');
            unset($filter['bulan']);
        }

        $this->_list($model,$filter,$offset,$limit,$orderBy);
        $this->dataAbsensi = $this->pagination;
        return $this->dataAbsensi;
    }

    public function getPaginationAbsensi($path = '')
    {
        if(!isset($this->dataAbsensi))$this->dataAbsensi = $this->pagination;
        return $this->_getPagination($path, $this->dataAbsensi);
    }

    public function getAbsensi($filter = false) 
    {
        $model = new MAbsensi();
        return $this->_getOne($model,$filter);
    }

    /**
     * generate absensi
     */
    public function generateDefaultAbsensiAll($filterPegawai=false,$shiftId=false,$startDate=false,$endDate=false,$insertOnly=false)
    {
        ini_set('memory_limit','1024M');
        set_time_limit(0);
        Log::info('GenerateDefaultAbsensiAll : '.json_encode($filterPegawai).' '.($insertOnly?'insert Only':''));
        $pegawais = Pegawai::with(['jabatan']);
        if($filterPegawai){
            $pegawais = $this->_where($pegawais,$filterPegawai);
        }
        $pegawais = $pegawais->get();
        foreach ($pegawais as $value) {
            $pegawai = $value->toArray();

            if($shiftId){
                $curShiftId = $shiftId;
            }else{
                $curShiftId = isset($pegawai['jabatan']['shift_id'])?$pegawai['jabatan']['shift_id']:0;
            }                

            if($startDate!=false && $endDate!=false ){                
                $this->generateAbsensi($pegawai['id'],$curShiftId,$startDate,$endDate,false,$insertOnly);
            }else{
                $this->generateOneMonthAbsensi($pegawai['id'],now()->format('Y-m-d'),$curShiftId,$insertOnly);
            }
            
        }
    }
    
    /**
     * generate absensi default selama 1 bulan ini berdasarkan tanggal yang diinput
     */
    public function generateOneMonthAbsensi($pegawaiId,$date,$shiftId,$insertOnly=false)
    {        
        $tgl = new Carbon($date);
        $jumlahHari = $tgl->daysInMonth;
        $curMonth = $tgl->format('Y-m-');        
        $this->generateAbsensi($pegawaiId,$shiftId,$curMonth.'01',$curMonth.$jumlahHari,false,$insertOnly);
    }

    /**
     * generate atau update data/record absensi berdasarkan rentang waktu, per 1 pegawai dan jam kerja shift nya
     * absen yang akan digenerate adalah :
     *      - yang belum ada
     *      - yang sudah ada tapi status 0 (baru generate, belum ada scan)
     *      - yang sudah ada tapi status 5 (libur dari libur shift dan libur fitur hari_libur)
     *      - yang sudah ada tapi status 6 (libur yg diset dari menu shift perorangan) khusus $byPassHariLibur = true
     *
     * @param $pegawaiId
     * @param $byPassHariLibur jika bypass hari libur (true) berarti yg libur manual pun tetep bisa diubah (digunakan saat set shift perorangan agar bisa set absensi di haris libur tetap masuk)
     * @param $forceUpdate jika true maka yg akan digenerate atau diupdate adalah semua tipe absen, termasuk yg sudah terkalkulasi juga akan di update ulang
     **/
    public function generateAbsensi($pegawaiId,$shiftId,$startDate,$endDate,$byPassHariLibur=false,$insertOnly=false,$forceUpdate=false)
    {
        // $_shiftData = is_array($shiftId)?http_build_query($shiftId):$shiftId;
        // Log::info('Start generate absensi : '.$pegawaiId.' - '.$_shiftData.' - '.$startDate.' - '.$endDate.' - '.$byPassHariLibur.' - '.$insertOnly);
        $weekMap = [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ];
        
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $value) {
            $dates[] = $value->format('Y-m-d');
        }

        // $dateNum = $date->daysInMonth;//jumlah hari di bulan ini

        if(!$shiftId)$shiftId=1;

        $hasRamadhan = false;
        $noShift = false;
        $shifts = [];

        //jika shift id array berarti langsung set jam masuk dan jam keluar tanpa mengacu ke shift
        if(is_array($shiftId)){
            if(!isset($shiftId['masuk']) || !isset($shiftId['pulang'])){
                $this->error = 'Parameter jam masuk dan keluar tidak lengkap';
                Log::info('generateAbsensi ERROR : Parameter jam masuk dan keluar tidak lengkap');
                return false;
            }
            $shift = $shifts[0] = $shiftId;
            $shiftId = 0;
        }else{
            //get shift 1
            $shift = ShiftDetail::where('shift_id',$shiftId)->where(function($q) use ($startDate, $endDate) {
                $q->where('tipe',0)->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('tipe',1)->where('range_awal','<=',$startDate)->where('range_akhir','>=',$endDate);
                });
            });
            
            if($shift->count()<=0){
                $noShift = true;
                $shiftId=0;
            }else if($shift->count()==2){
                $hasRamadhan = true;
                $shift = $shift->get();
                foreach($shift as $v) {
                    $shifts[$v->tipe] = $v->toArray();
                }
            }else{
                $shift = $shifts[0] = $shift->first()->toArray();
            }
        }
        

        //--- START - get all hari libur
        $libur = [];
        $hariLibur = HariLibur::where(function($q) use($startDate, $endDate) {
            $q->where('start','>=',$startDate)->where('start','<=',$endDate);
        })->orWhere(function($q) use($startDate, $endDate) {
            $q->where('end','>=',$startDate)->where('end','<=',$endDate);
        })->get();

        foreach ($hariLibur as $key => $value) {
            $period = CarbonPeriod::create($value->start, $value->end);
            foreach ($period as $val) {
                $a = $val->format('Y-m-d');
                $libur[$a] = $a;
            }
        }
        //--- END

        // Log::info('Generate absensi : number of dates '.count($dates));

        foreach($dates as $date){

            $tgl = new Carbon($date);

            $day = $tgl->dayOfWeek;//hari dalam minggu ini 0-6

            //secara default tanggal scan adalah lintas hari ke besok
            $dateKeluar = $tgl->addDay()->format('Y-m-d');
            // $nextDay = $tgl->dayOfWeek;//hari selnajutnya dalam minggu ini 0-6

            $oldAbsen = MAbsensi::where('tanggal',$date)->where('pegawai_id',$pegawaiId)->first();
            $isInsert = true;
            $status = 0;
            if($oldAbsen){
                $oldAbsen = $oldAbsen->toArray();
                $status = $oldAbsen['status'];
                $isInsert = false;
            }

            //jika khusus insert tapi bukan proses insert maka skip
            if($insertOnly && !$isInsert)continue;
            
            $absensiStatus = false;
            if($forceUpdate){
                $absensiStatus = true;
            }else{
                // 0:generate default; 1:hadir; 2:alpha; 3:scan tidak komplit; 4:ijin (ijinnya apa lihat di jenis_ijin_id); 5:libur hari libur atau shift ; 6:libur yang diset manual per pegawai
                
                // proses generate absensi jika absensi belum ada atau jika sudah ada tapi berstatus yang masih boleh diubah :
                // - status 0 : new generate
                // - status 5 : hari libur shift atau dari fitur hari_libur
                // - status 6 : libur yg diset per pegawai (jika di bypass)
                $allowedStatus = [0,5];
                //jika bypass hari libur berarti yg libur manual pun tetep bisa diubah
                if($byPassHariLibur)$allowedStatus[] = 6;
                $absensiStatus = $isInsert?true:in_array($oldAbsen['status'],$allowedStatus);                
            }

            if($status!=4)$status=0;

            if($isInsert || $absensiStatus){
                
                $isLibur = false;

                if($noShift){
                    if($weekMap[$day]=='sabtu' || $weekMap[$day]=='minggu'){
                        $isLibur = true;
                        $shift = [
                            $weekMap[$day].'_masuk' => null,
                            $weekMap[$day].'_pulang' => null
                        ];
                    }else{
                        $shift = [
                            $weekMap[$day].'_masuk' => '08:00:00',
                            $weekMap[$day].'_pulang' => '16:00:00'
                        ];
                    }
                }else if($shiftId==0){
                    
                    $shift = [
                        $weekMap[$day].'_masuk' => $shifts[0]['masuk'],
                        $weekMap[$day].'_pulang' => $shifts[0]['pulang']
                    ];
                
                }else{
                    //jika ramadan
                    if($hasRamadhan){
                        if($date >= $shifts[1]['range_awal'] && $date <= $shifts[1]['range_akhir']){  
                            // Log::info('generateAbsensi HAS RAMADAN : '.$date.' range : '.$shifts[1]['range_awal'].' - '.$shifts[1]['range_akhir']);                 
                            $shift = [
                                $weekMap[$day].'_masuk' => $shifts[1][$weekMap[$day].'_masuk'],
                                $weekMap[$day].'_pulang' => $shifts[1][$weekMap[$day].'_pulang']
                            ];
                        }else{
                            // Log::info('generateAbsensi HAS RAMADAN BUT NOT IN RANGE : '.$date.' range : '.$shifts[1]['range_awal'].' - '.$shifts[1]['range_akhir']);
                            $shift = [
                                $weekMap[$day].'_masuk' => $shifts[0][$weekMap[$day].'_masuk'],
                                $weekMap[$day].'_pulang' => $shifts[0][$weekMap[$day].'_pulang']
                            ];
                        }
                    }else{       
                        // Log::info('generateAbsensi not RAMADAN : '.$date.' range : '.$shifts[0]['range_awal'].' - '.$shifts[0]['range_akhir']);             
                        $shift = [
                            $weekMap[$day].'_masuk' => $shifts[0][$weekMap[$day].'_masuk'],
                            $weekMap[$day].'_pulang' => $shifts[0][$weekMap[$day].'_pulang']
                        ];
                    }

                }

                //jika hari libur yang diset di fitur hari_libur
                if($byPassHariLibur!=true && isset($libur[$date])){
                    $isLibur = true;
                    $shift = [
                        $weekMap[$day].'_masuk' => null,
                        $weekMap[$day].'_pulang' => null
                    ];
                }

                //jika jam masuk kosong berarti hari libur
                if(!$shift[$weekMap[$day].'_masuk']){
                    $isLibur = true;
                }

                $isLintasHari = false;

                //jika libur sesuai shift atau setinga hari libur
                if($isLibur){
                    $status = 5;//libur
                }else{
                    // $status = 0;//
                    $isLintasHari = $shift[$weekMap[$day].'_masuk'] >= $shift[$weekMap[$day].'_pulang'];                    
                }

                //jika tidak lintas hari maka tanggal keluar ubah kembali ke tanggal hari ini
                if(!$isLintasHari)$dateKeluar = $date;
                
                $data = [
                    'pegawai_id'=>$pegawaiId,
                    'is_lintas_hari'=>$isLintasHari,
                    'shift_id' => $shiftId,
                    'tanggal' => $date,

                    'status'=>$status
                ];

                //0:generate default; 
                //1:hadir; 
                //2:alpha; 
                //3:scan absen tidak lengkap; 
                //4:ijin (ijinnya apa lihat di jenis_ijin_id); 
                //5:libur hari libur atau shift ; 
                //6:libur yang diset manual per pegawai
                if($isLibur){
                    $data['jam_masuk'] = null;//jam masuk kerja sesuai jadwal / shift
                    $data['jam_masuk_mulai_scan'] = null;
                    $data['jam_masuk_akhir_scan'] = null;
                    
                    $data['jam_keluar'] = null;//jam keluar kerja sesuai jadwal / shift
                    $data['jam_keluar_mulai_scan'] = null;
                    $data['jam_keluar_akhir_scan'] = null;
                }else{                    
                    $data['jam_masuk'] = $date.' '.$shift[$weekMap[$day].'_masuk'];
                    $data['jam_keluar'] = $dateKeluar.' '.$shift[$weekMap[$day].'_pulang'];
                    
                    $jamKerjaMasuk = new Carbon($data['jam_masuk']);
                    $setengahJamKerja =  ceil($jamKerjaMasuk->diffInMinutes($data['jam_keluar'], false)/2);
                    
                    $data['jam_masuk_mulai_scan'] = (new Carbon($data['jam_masuk']))->subMinutes(180);
                    $data['jam_masuk_akhir_scan'] = (new Carbon($data['jam_masuk']))->addMinutes($setengahJamKerja);
                    
                    $data['jam_keluar_mulai_scan'] = (new Carbon($data['jam_keluar']))->subMinutes($setengahJamKerja);
                    $data['jam_keluar_akhir_scan'] = (new Carbon($data['jam_keluar']))->addMinutes(480);
                }
                
                if($isInsert) {
                    if(!MAbsensi::where('tanggal',$date)->where('pegawai_id',$pegawaiId)->exists())
                        MAbsensi::create($data);
                    
                }else{
                    MAbsensi::where('tanggal',$date)->where('pegawai_id',$pegawaiId)->update($data);
                    
                    //jika update di data yg sudah ada scan nya maka langsung kalkulasi
                    if($oldAbsen['scan_masuk'] || $oldAbsen['scan_masuk']){
                        $this->kalkulasiAbsensi([
                            'status' => $data['status'],
                            'scan_masuk' => $oldAbsen['scan_masuk'],
                            'scan_keluar' => $oldAbsen['scan_keluar'],
                            'jam_masuk' => $data['jam_masuk'],
                            'jam_keluar' => $data['jam_keluar'],
                            'tanggal' => $date,
                            'id' => $oldAbsen['id']
                        ],true,true);
                    }
                }
            }
        }
        return true;
        // Log::info('Generate absensi : DONE');
    }

    /**
     * set libur perorangan per rentang waktu
     */
    public function setLibur($pegawaiId,$startDate,$endDate)
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $value) {
            $date = $value->format('Y-m-d');

            $data = [
                'shift_id' => 0,
                'jam_masuk' => null,
                'jam_masuk_mulai_scan' => null,
                'jam_masuk_akhir_scan' => null,
                'jam_keluar' => null,
                'jam_keluar_mulai_scan' => null,
                'jam_keluar_akhir_scan' => null,
                'status' => 6 //libur yang di set perorangan
            ];

            MAbsensi::where('tanggal',$date)->where('pegawai_id',$pegawaiId)->where(function($q) {
                //khusus status baru generate (0) dan libur yg sesuai shift atau fitur libur (5) atau libur manual (6)
                $q->where('status',0)->orWhere('status',5)->orWhere('status',6);
            })->update($data);
        }
        return true;
        
    }

    /**
     * ubah status ke alpa (2) atau telat/scan tidak komplit (3) untuk absen yg tidak libur
     * dieksekusi dari cron tiap jam 1 malam
     */
    public function kalkulasiAlpaNTelat() {
        $yeseterday = now()->yesterday()->format('Y-m-d');
    }
}