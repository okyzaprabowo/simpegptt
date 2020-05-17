<?php

namespace App\MainApp\Modules\Absensi\Controllers;

use Illuminate\Http\Request; 
use App\Base\BaseController;

use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Repositories\Absensi;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use App\MainApp\Jobs\InsertFromFileRaw;

class AbsensiController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }

    //
    public function index(Request $request)
    {
        
        if(!UserAuth::hasAccess('Master.mesinabsen')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->output['data']['mesin'] = Master::listMesinAbsen();
        
        $orderBy = false;
        $this->output['data']['filter'] = //untuk filter ke repo
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
        $limit['limit'] = $request->input('limit', 10);
        $this->output['data']['filter'][] = ['is_from_file',1];

        $this->output['data']['absensiRawUpload'] = Absensi::listAbsensiRawUpload(
            $this->output['data']['filter'], $limit['offset'], $limit['limit'],$orderBy
        );
        $this->output['viewdata']['pagination'] = Absensi::getPaginationAbsensiRawUpload(route('absensi_upload', $paginationParams));

        $this->response = 'absensi.form';
        
        return $this->done();
    }

    public function create(Request $request)
    {
        $this->response = redirect()->route('absensi_upload');
        
        if($request->file('data_file',null)){
            $mesinId = $request->input('mesin_absen_id');
            if($filePath = Absensi::uploadFileRaw($mesinId,$request->file('data_file'))){
                InsertFromFileRaw::dispatch($filePath->id);   
                $this->output['message'] = 'Upload Berhasil';
                return $this->done();
            }     
        }

        $this->setAlert('Upload error, data file belum diisi','danger');        
        return $this->done();
    }

    public function processRaw(Request $request)
    {                                                                                                                                                                                                                                                $data = Absensi::listAbsensiRawUpload([['status',2]]);
        if($data['count']==0){
            $this->setWarning('Tidak ada data raw yang siap diproses.');
        }else{                                                                                                                                                                                                                                           \App\MainApp\Jobs\CalculateAbsensi::dispatch();
            $this->setAlert('Upload error, data file belum diisi','danger');
        }
        return $this->done();
    }

    public function autoProcess(Request $request)
    {
        $data = Absensi::listAbsensiRawUpload([['status',2]]);
        if($data['count']>0){
            \App\MainApp\Jobs\CalculateAbsensi::dispatch();
	    return 'true';
        }
        return 'false';
    }
    
    /**
     * delete upload absen beserta data raw nya
     */
    public function delete(Request $request)
    {
        $this->response = redirect()->route('absensi_upload');

        $id = $request->route('id');

        if(Absensi::deleteAbsensiRawUpload($id)){
            $this->output['message'] = 'Data berhasil dihapus';
        }else{
            $this->setError(Absensi::error());
        }
        
        return $this->done();

    }

    /**
     * 
     * -----------------------------------------------------
     */

    /**
     * detail / list raw data absen berdasarkan file upload nya
     * 
     * @return data :
     *      absensiRawUpload
     *      absensiRaw
     *      filter
     */
    public function detail(Request $request)
    {
        $fileId = $request->route('id');
        
        $orderBy = false;
        $this->output['data']['filter'] = //untuk filter ke repo
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
        $limit['limit'] = $request->input('limit', 10);
        $this->output['data']['absensiRawUpload'] = Absensi::getAbsensiRawUpload($fileId);

        $this->output['data']['filter'][] = ['absensi_raw_upload_id',$fileId];
        $this->output['data']['absensiRaw'] = Absensi::listAbsensiRaw(
            $this->output['data']['filter'], $limit['offset'], $limit['limit'],$orderBy
        );
        $paginationParams['id'] = $fileId;
        $this->output['viewdata']['pagination'] = Absensi::getPaginationAbsensiRaw(route('absensi_upload.detail', $paginationParams));

        $this->response = 'absensi.detail';
        
        return $this->done();

    }
    
    /**
     * delete 1 data raw
     */
    public function detailDelete(Request $request)
    {        
        $this->response = redirect()->route('absensi_upload.detail');

        $id = $request->route('id');

        if(Absensi::deleteAbsensiRaw($id)){
            $this->output['message'] = 'Data berhasil dihapus';
        }else{
            $this->setError(Absensi::error());
        }
        
        return $this->done();
    }
}
