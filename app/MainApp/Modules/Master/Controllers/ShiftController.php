<?php

namespace App\MainApp\Modules\Master\Controllers;

use Illuminate\Http\Request; 
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Base\BaseController;

class ShiftController extends BaseController
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
        if(!UserAuth::hasAccess('Master.shift')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'shift.list';

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

        $this->output['data'] = Master::listShift(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        //generate pagination
        $this->output['viewdata']['pagination'] = Master::getPaginationShift(route('master.shift.list', $paginationParams));
        $this->output['data']['instansi'] = Master::listInstansiPerParent();
        return $this->done();
    }

    /**
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'shift.form';
        
        return $this->done();
    }
    /**
     * GET - form detail
     */
    public function detail(Request $request)
    {
        $this->response = 'shift.detail';
        
        $this->output['data']['shift'] = Master::getShift($request->id);  
        // var_dump($this->output['data']['shift']);die;
       // $this->output['data']['shiftDetail'] = Master::getShiftDetail($request->id);  
        return $this->done();
    }

    /**
     * POST - save data baru
     */
    public function create(Request $request)
    {
        $this->response = redirect()->route('master.shift.list');

        $data = $request->all();
        $editmode = ($request->id!="");
        if($data){
            $rules = [
                'nama' => ["required",
                    //"unique:cabang", 
                    ($editmode? Rule::unique('shift')->ignore($request->id): Rule::unique('shift')),
                    ],
                 
           
            ];
            $messages = [
                'nama.required' => 'Nama harus diisi',
                'nama.unique' => 'Nama cabang sudah ada',
              
            ];
            $validator = Validator::make($data, $rules,$messages);
            if ($validator->fails()){
                $this->setError('Penyimpanan gagal');
                $this->response = redirect()->route('master.shift.addNew'); 
            }else{
                $this->output['message'] = 'Data berhasil disimpan';
                unset($data['shift_detail']);
                $this->output['data'] = Master::createShift($data);  
                //redirect ke inputan detail shift;
                $this->response = redirect()->route('master.shift.detail',['shift_id'=>$this->output['data']['id']]);    
            }

                  
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('master.shift.addNew');
        }
        
        return $this->done();

    }

    public function createDetail(Request $request)
    {
        $this->response = redirect()->route('master.shift.list');
        
        $data = $request->all();
        $editmode = ($request->id!="");
        if($data){
            $rules = [
                // 'nama' => ["required",
                    //"unique:cabang", 
                    // ($editmode? Rule::unique('shift')->ignore($request->id): Rule::unique('shift')),
                   // ]
                 
           
            ];
            $messages = [
             //   'nama.required' => 'Nama harus diisi',
            //    'nama.unique' => 'Nama cabang sudah ada',
              
            ];


            if ($data['senin_masuk']=='') $data['senin_masuk'] = null;
            if ($data['senin_pulang']=='') $data['senin_pulang'] = null; 
            if ($data['selasa_masuk']=='') $data['selasa_masuk'] = null;
            if ($data['selasa_pulang']=='') $data['selasa_pulang'] = null; 
            if ($data['rabu_masuk']=='') $data['rabu_masuk'] = null;
            if ($data['rabu_pulang']=='') $data['rabu_pulang'] = null; 
            if ($data['kamis_masuk']=='') $data['kamis_masuk'] = null;
            if ($data['kamis_pulang']=='') $data['kamis_pulang'] = null; 
            if ($data['jumat_masuk']=='') $data['jumat_masuk'] = null;
            if ($data['jumat_pulang']=='') $data['jumat_pulang'] = null; 
            if ($data['sabtu_masuk']=='') $data['sabtu_masuk'] = null;
            if ($data['sabtu_pulang']=='') $data['sabtu_pulang'] = null; 
            if ($data['minggu_masuk']=='') $data['minggu_masuk'] = null;
            if ($data['minggu_pulang']=='') $data['minggu_pulang'] = null;  
            if ($data['range_awal']=='') $data['range_awal'] = null;  
            if ($data['range_akhir']=='') $data['range_akhir'] = null;  
            // $validator = Validator::make($data, $rules,$messages);
            // if ($validator->fails()){
            //     $this->setError('Penyimpanan gagal');
            //     $this->response = redirect()->route('master.shift.addNew'); 
            // }else{
                $this->output['message'] = 'Data berhasil disimpan';
                
                $this->output['data'] = Master::createShiftDetail($data);  
                //redirect ke inputan detail shift;
                $this->response = redirect()->route('master.shift.detail',['shift_id'=>$this->output['data']['shift_id']]);    
            // }

                  
        }else{
            $this->setError('Data Not Found');
            $this->response = redirect()->route('master.shift.addNew');
        }
        
        return $this->done();

    }
    /**
     * GET - get 1 data atau memunculkan form edit
     */
    public function edit(Request $request)
    {
        $this->response = 'shift.form';
        
        $id = $request->route('id');

        $this->output['data'] = Master::getShift($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('master.shift.list');
        }

        return $this->done();

    }
    /**
     * PUT - update edit data
     */
    public function update(Request $request)
    {
        $this->response = redirect()->route('master.shift.list');
        
        $id = $request->route('id');
        $data = $request->all();
        
        $this->output['message'] = 'Data berhasil diupdate';
        unset($data['shift_detail']);
        $this->output['data'] = Master::updateShift(['id',$id],$data);  
        if(!$this->output['data']){
            $this->setError(Master::error());
            //jika error redirect ke halaman form
            $this->response = redirect()->route('master.shift.list',['id'=>$id]);
        }        
        return $this->done();

    }
    public function updateDetail(Request $request)
    {
        $this->response = redirect()->route('master.shift.list');
        
        $id = $request->route('id');
        $data = $request->all();
        if ($data['senin_masuk']=='') $data['senin_masuk'] = null;
        if ($data['senin_pulang']=='') $data['senin_pulang'] = null; 
        if ($data['selasa_masuk']=='') $data['selasa_masuk'] = null;
        if ($data['selasa_pulang']=='') $data['selasa_pulang'] = null; 
        if ($data['rabu_masuk']=='') $data['rabu_masuk'] = null;
        if ($data['rabu_pulang']=='') $data['rabu_pulang'] = null; 
        if ($data['kamis_masuk']=='') $data['kamis_masuk'] = null;
        if ($data['kamis_pulang']=='') $data['kamis_pulang'] = null; 
        if ($data['jumat_masuk']=='') $data['jumat_masuk'] = null;
        if ($data['jumat_pulang']=='') $data['jumat_pulang'] = null; 
        if ($data['sabtu_masuk']=='') $data['sabtu_masuk'] = null;
        if ($data['sabtu_pulang']=='') $data['sabtu_pulang'] = null; 
        if ($data['minggu_masuk']=='') $data['minggu_masuk'] = null;
        if ($data['minggu_pulang']=='') $data['minggu_pulang'] = null;  
        if ($data['range_awal']=='') $data['range_awal'] = null;  
        if ($data['range_akhir']=='') $data['range_akhir'] = null;  
        $this->output['message'] = 'Data berhasil diupdate';
        $this->output['data'] = Master::updateShiftDetail(['id',$id],$data);  
        
        if(!$this->output['data']){
            $this->setError(Master::error());
            //jika error redirect ke halaman form
            // $this->response = redirect()->route('master.shift.detail',['shift_id'=>$data['shift_id'],'id'=>$id]);
            $this->response = redirect()->route('master.shift.detail',['shift_id'=>$this->output['data']['shift_id']]);    
        }        
        return $this->done();

    }

    /**
     * DELETE - delete data
     */
    public function delete(Request $request) 
    {        
        $this->response = redirect()->route('master.shift.list');

        $id = $request->route('id');

        if(Master::deleteShift($id)){
            $this->output['message'] = 'Data berhasil dihapus';
        }else{
            $this->setError(Master::error());
        }
        
        return $this->done();
    }
}