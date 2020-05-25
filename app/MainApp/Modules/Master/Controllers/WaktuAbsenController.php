<?php

namespace App\MainApp\Modules\Master\Controllers;

use Illuminate\Http\Request; 
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use Illuminate\Support\Facades\DB;

use App\Base\BaseController;

class WaktuAbsenController extends BaseController
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
        if(!UserAuth::hasAccess('superadmin')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'waktu_absen.list';

        $result = DB::table('waktu_absen')->get();
        if($result){
            $data = [
                'jam_masuk_mulai_scan' => $result[0]->jam_masuk_mulai_scan,
                'jam_masuk_akhir_scan' => $result[0]->jam_masuk_akhir_scan,
                'jam_keluar_mulai_scan' => $result[0]->jam_keluar_mulai_scan,
                'jam_keluar_akhir_scan' => $result[0]->jam_keluar_akhir_scan,
            ];
        }else{
            $data = [];
        }

        $this->output['data'] = $data;

        return $this->done();
    }

    public function update(Request $request)
    {
        $jam_masuk_mulai_scan = $request->input('jam_masuk_mulai_scan');
        $jam_masuk_akhir_scan = $request->input('jam_masuk_akhir_scan');
        $jam_keluar_mulai_scan = $request->input('jam_keluar_mulai_scan');
        $jam_keluar_akhir_scan = $request->input('jam_keluar_akhir_scan');

        if(!is_numeric($jam_masuk_mulai_scan)){
            $jam_masuk_mulai_scan = 0;
        }
        if(!is_numeric($jam_masuk_akhir_scan)){
            $jam_masuk_akhir_scan = 0;
        }
        if(!is_numeric($jam_keluar_mulai_scan)){
            $jam_keluar_mulai_scan = 0;
        }
        if(!is_numeric($jam_keluar_akhir_scan)){
            $jam_keluar_akhir_scan = 0;
        }

        $data = [
            'jam_masuk_mulai_scan' => $jam_masuk_mulai_scan,
            'jam_masuk_akhir_scan' => $jam_masuk_akhir_scan,
            'jam_keluar_mulai_scan' => $jam_keluar_mulai_scan,
            'jam_keluar_akhir_scan' => $jam_keluar_akhir_scan
        ];   

        $update = DB::table('waktu_absen')->update($data);

        $this->output['message'] = 'Data berhasil diupdate';
        $this->response = redirect()->route('master.waktu_absen.list');
        return $this->done();
    }

}