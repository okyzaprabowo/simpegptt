<?php

namespace App\MainApp\Modules\moduser\Controllers;

use Illuminate\Http\Request;

use Facades\App\MainApp\Modules\moduser\Repositories\RoleRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use App\Base\BaseController;

class RoleController extends BaseController
{
    public function __construct()
    {
        $this->forceApiOutput();
    }

    /**
     * GET /api/user/role
     * 
     * list pengajuan
     * 
     * @param Request $request
     * 
     */
    public function readList(Request $request)
    {

        $orderBy = false;
        $filter = [];

        if($request->input('q', false))
            $filter['q'] = $request->input('q');

        //jika menyertakan status
        if($request->input('status', false))
            $filter[] = ['status', $request->input('status')];

        if(UserAuth::isLogin()){
            $roles = explode(';',trim(UserAuth::user('role'),';'));
            foreach($roles as $role){
                $filter[] = ['role_code','!=',$role];
            }
            $filter[] = ['level','>',UserAuth::user('level')];
        }
        
        //jika multitenant aktif dan bukan dari aplikasi owner maka filter berdasarkan tenant nya
        if (config('AppConfig.system.web_admin.multitenant.active')==1 && config('tenant.id')>1) {
            $filter[] = [
                ['tenant_id', config('tenant.id')],
                ['OR tenant_group_id',config('tenant.tenant_group_id')],
                [
                    'OR',
                    ['tenant_id',0],['tenant_group_id',0]
                ]
            ];
        }

        //jika menyertakan order by
        if ($request->input('orderBy', false)){
            $orderBy = $request->input('orderBy');

            if($orderBy=='tenant'){
                $orderBy = 'tenant_id';
            }else if($orderBy=='tenant_group'){
                $orderBy = 'tenant_group_id';
            }

            $orderBy = [$request->input('orderBy'), $request->input('orderType', 'ASC')];
        }
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 0);

        $this->output['data'] = RoleRepo::listRole(            
            $filter,
            $limit['offset'],
            $limit['limit'],
            $orderBy
        );

        return $this->done();
    }

    /**
     * GET /api/user/role/{id}
     * 
     * Route Param : 
     *      id : route id
     */
    public function readOne(Request $request) {

        $id = $request->route('id');
        $this->output['data'] = RoleRepo::getRole($id);

        if(!$this->output['data']){
            $this->setError('Data Not Found');
        }
        return $this->done();
    }

    /**
     * POST /api/user/role
     * 
     * @param Request $request 
     *      role_code String
     *      level Integer 
     *      name String
     *      rule Array rule format
     * 
     *      *option
     *      tenant_group_id
     *      tenant_id
     */
    public function create(Request $request) 
    {
        $input = $request->all();//$request->only(['name', 'email', 'password']);

        $validator = [
            'role_code' => 'required|min:3|max:191',
            'level' => 'required',
            'name' => 'required|min:3|max:191',
            'rule' => 'required'
        ];

        $validator = \Validator::make($input, $validator);

        if(isset($input['id']))unset($input['id']);
        if(!isset($input['tenant_id']))$input['tenant_id'] = 0;
        if(!isset($input['tenant_group_id']))$input['tenant_group_id'] = 0;

        if ($validator->fails()) {       
            $this->setError(__('validation.inputerror'),$validator->messages());
            return $this->done();
        }
        
        //jika berhasil
        if ($user = RoleRepo::createRole($input,false)) {
            $this->setAlert('Data Inserted successfully','success');
        }else{
            $this->setError(RoleRepo::error(),'success');
        }
        
        return $this->done();
        
    }

    public function update(Request $request) {
        
        $id = $request->route('id');
        $input = $request->all();
        
        if(isset($input['id']))unset($input['id']);

        $this->output['message'] = 'Data berhasil diupdate';
        $this->output['data'] = RoleRepo::updateRole(['id',$id],$input);            

        if(!$this->output['data']) {
            $this->setError('Update failed : '.RoleRepo::error());
        }
        return $this->done();
    }

    public function delete(Request $request) {

        $id = $request->route('id');
           
        if(UserAuth::isLogin()){//} && $id != UserAuth::user('id')){
            // $filter[] = ['id', $id];
            $filter[] = ['level','>',UserAuth::user('level')];
            $data = RoleRepo::listRole($filter);
            if($data['count']<=0){
                $this->setError('Permission denied');
                return $this->done();;
            }
        }   

        if(!RoleRepo::deleteRole($id)){
            $this->setError('Error : '.RoleRepo::error());
        }
        return $this->done();
        
    }
}
