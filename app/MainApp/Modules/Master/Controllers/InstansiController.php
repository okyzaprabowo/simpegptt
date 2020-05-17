<?php

namespace App\MainApp\Modules\Master\Controllers;

use Illuminate\Http\Request; 
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use App\Base\BaseController;

class InstansiController extends BaseController
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
        if(!UserAuth::hasAccess('Master.instansi')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }
        
        $this->response = 'instansi.list';

        $this->output['data'] = Master::listInstansiPerParent();

        return $this->done();
    }

    /**
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'instansi.form';
        
        return $this->done();
    }

    /**
     * POST - save data baru
     */
    public function create(Request $request)
    {
        $this->response = redirect()->route('master.instansi.list');

        $data = $request->all();
        if($data){
            $this->output['message'] = 'Data berhasil disimpan';
            $this->output['data'] = Master::createInstansi($data);            
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('master.instansi.addNew');
        }
        
        return $this->done();

    }
    /**
     * GET - get 1 data atau memunculkan form edit
     */
    public function edit(Request $request)
    {
        $this->response = 'instansi.form';
        
        $id = $request->route('id');

        $this->output['data'] = Master::getInstansi($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('master.instansi.list');
        }

        return $this->done();

    }
    /**
     * PUT - update edit data
     */
    public function update(Request $request)
    {
        $this->response = redirect()->route('master.instansi.list');
        
        $id = $request->route('id');
        $data = $request->all();
        
        $this->output['message'] = 'Data berhasil diupdate';
        $this->output['data'] = Master::updateInstansi(['id',$id],$data);  
        if(!$this->output['data']){
            $this->setError(Master::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('master.instansi.edit',['id'=>$id]);
        }        
        return $this->done();

    }

    /**
     * DELETE - delete data
     */
    public function delete(Request $request) 
    {        
        $this->response = redirect()->route('master.instansi.list');

        $id = $request->route('id');

        if(Master::deleteInstansi($id)){
            $this->output['message'] = 'Data berhasil ';
        }else{
            $this->setError(Master::error());
        }
        
        return $this->done();

    }
}