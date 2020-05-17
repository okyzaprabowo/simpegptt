<?php

namespace App\MainApp\Modules\Master\Controllers;

use Illuminate\Http\Request; 
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Base\BaseController;

class JabatanController extends BaseController
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
        if(!UserAuth::hasAccess('Master.jabatan')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'jabatan.list';

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

        $this->output['data'] = Master::listJabatan(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        //generate pagination
        $this->output['viewdata']['pagination'] = Master::getPaginationJabatan(route('master.jabatan.list', $paginationParams));
        $this->output['data']['instansi'] = Master::listInstansiPerParent();
        $this->output['viewdata']['shift'] = Master::listShift();
        return $this->done();
    }

    /**
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'jabatan.form';
        
        return $this->done();
    }

    /**
     * POST - save data baru
     */
    public function create(Request $request)
    {
        $this->response = redirect()->route('master.jabatan.list');

        $data = $request->all();
       
        $editmode = $data['id']<>'';
        if($data){
            $rules = [
                'nama' => ["required",
                    //"unique:cabang", 
                    ($editmode? Rule::unique('jabatan')->ignore($request->id): Rule::unique('jabatan')),
                    ],
                 
           
            ];
            $messages = [
                'nama.required' => 'Nama harus diisi',
                'nama.unique' => 'Nama jabatan sudah ada',
              
            ];
            $validator = Validator::make($data, $rules,$messages);
            if ($validator->fails()){
                $this->setError('Penyimpanan gagal');
                $this->response = redirect()->route('master.jabatan.addNew'); 
            }else{
                $this->output['message'] = 'Data berhasil disimpan';
                // $data['instansi_ids'] = implode(',',$data['instansi_ids']);
                $items = $data['jabatan_instansi'];
               
                // foreach ($items as $key => $value){
                //     var_dump($value['instansi_id']);die;
                // }
                $this->output['data'] = Master::createJabatan($data);      
            }

                  
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('master.jabatan.addNew');
        }
        
        return $this->done();

    }
    /**
     * GET - get 1 data atau memunculkan form edit
     */
    public function edit(Request $request)
    {
        $this->response = 'jabatan.form';
        
        $id = $request->route('id');

        $this->output['data'] = Master::getJabatan($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('master.jabatan.list');
        }

        return $this->done();

    }
    /**
     * PUT - update edit data
     */
    public function update(Request $request)
    {
        $this->response = redirect()->route('master.jabatan.list');
        
        $id = $request->route('id');
        $data = $request->all();
        // $data['instansi_ids'] = implode(',',$data['instansi_ids']);
        $this->output['message'] = 'Data berhasil diupdate';
        $this->output['data'] = Master::updateJabatan(['id',$id],$data);  
        if(!$this->output['data']){
            $this->setError(Master::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('master.jabatan.edit',['id'=>$id]);
        }        
        return $this->done();

    }

    /**
     * DELETE - delete data
     */
    public function delete(Request $request) 
    {        
        $this->response = redirect()->route('master.jabatan.list');

        $id = $request->route('id');

        if(Master::deleteJabatan($id)){
            $this->output['message'] = 'Data berhasil dihapus';
        }else{
            $this->setError(Master::error());
        }
        
        return $this->done();
    }
}