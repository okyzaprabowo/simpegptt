<?php

namespace App\MainApp\Modules\Master\Controllers;

use Illuminate\Http\Request; 
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use App\Base\BaseController;

class JenisIjinController extends BaseController
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
        if(!UserAuth::hasAccess('Master.jenisijin')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'jenis_ijin.list';

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
        $limit['limit'] = $request->input('limit', 10);

        $this->output['data'] = Master::listJenisIjin(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        //generate pagination
        $this->output['viewdata']['pagination'] = Master::getPaginationJenisIjin(route('master.jenis_ijin.list', $paginationParams));
        
        $this->output['data']['jenisIjin'] = Master::listJenisIjinKategori(
            false, 0, 0
        );
        return $this->done();
    }

    /**
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'jenis_ijin.form';
        
        return $this->done();
    }

    /**
     * POST - save data baru
     */
    public function create(Request $request)
    {
        $this->response = redirect()->route('master.jenis_ijin.list');

        $data = $request->all();
        if($data){
            $this->output['message'] = 'Data berhasil disimpan';
            $this->output['data'] = Master::createJenisIjin($data);            
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('master.jenis_ijin.addNew');
        }
        
        return $this->done();

    }
    /**
     * GET - get 1 data atau memunculkan form edit
     */
    public function edit(Request $request)
    {
        $this->response = 'jenis_ijin.form';
        
        $id = $request->route('id');

        $this->output['data'] = Master::getJenisIjin($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('master.jenis_ijin.list');
        }

        return $this->done();

    }
    /**
     * PUT - update edit data
     */
    public function update(Request $request)
    {
        $this->response = redirect()->route('master.jenis_ijin.list');
        
        $id = $request->route('id');
        $data = $request->all();
        
        $this->output['message'] = 'Data berhasil diupdate';
        $this->output['data'] = Master::updateJenisIjin(['id',$id],$data);  
        if(!$this->output['data']){
            $this->setError(Master::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('master.jenis_ijin.edit',['id'=>$id]);
        }        
        return $this->done();

    }

    /**
     * DELETE - delete data
     */
    public function delete(Request $request) 
    {        
        $this->response = redirect()->route('master.jenis_ijin.list');

        $id = $request->route('id');

        if(Master::deleteJenisIjin($id)){
            $this->output['message'] = 'Data berhasil ';
        }else{
            $this->setError(Master::error());
        }
        
        return $this->done();

    }

    
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
    public function kategoriIndex(Request $request)
    {
        if(!UserAuth::hasAccess('Master.jenisijin.kategori')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }
        $this->response = 'jenis_ijin.kategoriList';

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
        $limit['limit'] = $request->input('limit', 10);

        $this->output['data'] = Master::listJenisIjinKategori(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        //generate pagination
        $this->output['viewdata']['pagination'] = Master::getPaginationJenisIjinKategori(route('master.jenis_ijin_kategori.list', $paginationParams));
        
        return $this->done();
    }

    /**
     * GET - form add new
     */
    public function kategoriAddNew(Request $request)
    {
        $this->response = 'jenis_ijin.kategoriForm';
        
        return $this->done();
    }

    /**
     * POST - save data baru
     */
    public function kategoriCreate(Request $request)
    {
        $this->response = redirect()->route('master.jenis_ijin_kategori.list');

        $data = $request->all();
        if($data){
            $this->output['message'] = 'Data berhasil disimpan';
            $this->output['data'] = Master::createJenisIjinKategori($data);            
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('master.jenis_ijin_kategori.addNew');
        }
        
        return $this->done();

    }
    /**
     * GET - get 1 data atau memunculkan form edit
     */
    public function kategoriEdit(Request $request)
    {
        $this->response = 'jenis_ijin.kategoriForm';
        
        $id = $request->route('id');

        $this->output['data'] = Master::getJenisIjinKategori($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('master.jenis_ijin_kategori.list');
        }

        return $this->done();

    }
    /**
     * PUT - update edit data
     */
    public function kategoriUpdate(Request $request)
    {
        $this->response = redirect()->route('master.jenis_ijin_kategori.list');
        
        $id = $request->route('id');
        $data = $request->all();
        
        $this->output['message'] = 'Data berhasil diupdate';
        $this->output['data'] = Master::updateJenisIjinKategori(['id',$id],$data);  
        if(!$this->output['data']){
            $this->setError(Master::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('master.jenis_ijin.edit',['id'=>$id]);
        }        
        return $this->done();

    }

    /**
     * DELETE - delete data
     */
    public function kategoriDelete(Request $request) 
    {        
        $this->response = redirect()->route('master.jenis_ijin_kategori.list');

        $id = $request->route('id');

        if(Master::deleteJenisIjinKategori($id)){
            $this->output['message'] = 'Data berhasil ';
        }else{
            $this->setError(Master::error());
        }
        
        return $this->done();

    }
}