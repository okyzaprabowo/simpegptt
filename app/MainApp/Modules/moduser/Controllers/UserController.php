<?php

namespace App\MainApp\Modules\moduser\Controllers;

use Illuminate\Http\Request;
use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Repositories\RoleRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Facades\App\MainApp\Repositories\Master;

use App\Base\BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }
    
    /**
     * GET /api/user
     * 
     */
    public function readList(Request $request) 
    {
        if(!UserAuth::hasAccess('moduser.user')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }

        $this->response = 'user.list';
        
        $orderBy = false;
        $filter = [];        
        $paginationParams = [];


        if($request->input('q', false)){
            $filter['q'] = $request->input('q');
            $paginationParams = //untuk parameter get ke pagination
            [
                'q'=>$request->input('q', null)
            ];
        }

        //jika menyertakan order by
        if($request->input('orderBy', null)){
            $orderBy = [$request->input('orderBy'),$request->input('orderType','ASC')];

            $paginationParams['orderBy'] =$orderBy[0];
            $paginationParams['orderType'] =$orderBy[1];
        }

        //jika menyertakan status
        if($request->input('status', false))
            $filter[] = ['status', $request->input('status')];
        
        if(UserAuth::isLogin()){
            $filter[] = ['id','!=',UserAuth::user('id')];
            $filter[] = ['level','>',UserAuth::user('level')];
        }     
        
        //jika menyertakan status
        if($request->input('role_code', false))
            $filter[] = ['role_code', 'LIKE', '%;'.$request->input('role_code').';%'];

        //jika multitenant aktif dan bukan dari aplikasi owner maka filter berdasarkan tenant nya
        if (config('AppConfig.system.web_admin.multitenant.active')==1 && config('tenant.id')) {
            $filter['tenant'] = [config('tenant.id')];
        }

        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 10);
        
        $this->output['data'] = UserRepo::listUser(
            $filter,
            $limit['offset'],
            $limit['limit'],
            $orderBy
        );
        // dd($this->output['data']['data']);
        if($this->isWebCall()){
            $this->output['viewdata']['pagination'] = UserRepo::getPaginationUser(route('user.list', $paginationParams));
        }        

        return $this->done();
    }

    /**
     * WEB Only
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'user.form';
        
        $this->output['data']['mode'] = 'add';
        $this->output['data']['data'] = [];
        $roles = RoleRepo::listRole(['max_level'=>UserAuth::user('level')]);
        $this->output['data']['roles'] = $roles['data'];
        $this->output['data']['instansiList'] = Master::listInstansi();
        
        $this->output['data']['instansiIds'] = [1];
        
        return $this->done();
    }
    
    /**
     * GET /api/user/{id}
     * 
     * Route Param : 
     *      id : route id
     */
    public function readOne(Request $request)
    {
        $this->response = 'user.form';
        $id = $request->route('id');

        $this->output['data']['data'] = UserRepo::getUser($id);
        $this->output['data']['user_role'] = UserRepo::getUserRole($this->output['data']['data']['id']);

        foreach($this->output['data']['user_role'] as $key => $val) {
            if($val['is_main_role']){
                $this->output['data']['role_code'] = $key;
            }
        }         
        $this->output['data']['mode'] = 'edit';

        $this->output['data']['instansi'] = Master::listInstansiPerParent();
        
        $this->output['data']['instansiIds'] = Master::getInstansi($this->output['data']['data']['profile']['instansi_id']);
        $this->output['data']['instansiIds'] = explode(';',trim($this->output['data']['instansiIds']['induk_path'],';'));
        $this->output['data']['instansiIds'][] = $this->output['data']['data']['profile']['instansi_id'];
        
        if(!$this->output['data']['data']['profile']['instansi_id'] || !$this->output['data']['instansiIds'][0])
            $this->output['data']['instansiIds'] = [1];
        
        $this->output['data']['instansiList'] = Master::listInstansi();

        if($this->isWebCall()){
            $roles = RoleRepo::listRole(['max_level'=>UserAuth::user('level')]);
            $this->output['data']['roles'] = $roles['data'];
        }

        return $this->done();
    }
    /**
     * WEB /user/{id}/view
     * 
     * Route Param : 
     *      id : route id
     */
    public function view(Request $request)
    {
        $this->response = 'user.form';
        $id = $request->route('id');

        $this->output['data']['data'] = UserRepo::getUser($id);
        $this->output['data']['user_role'] = UserRepo::getUserRole($this->output['data']['data']['id']);

        foreach($this->output['data']['user_role'] as $key => $val) {
            if($val['is_main_role']){
                $this->output['data']['role_code'] = $key;
            }
        } 
        $this->output['data']['mode'] = 'view';
        
        $this->output['data']['instansiIds'] = Master::getInstansi($this->output['data']['data']['profile']['instansi_id']);
        $this->output['data']['instansiIds'] = explode(';',trim($this->output['data']['instansiIds']['induk_path'],';'));
        $this->output['data']['instansiIds'][] = $this->output['data']['data']['profile']['instansi_id'];
        
        if(!$this->output['data']['data']['profile']['instansi_id'] || !$this->output['data']['instansiIds'][0])
            $this->output['data']['instansiIds'] = [1];
        
        $this->output['data']['instansiList'] = Master::listInstansi();
        if($this->isWebCall()){
            $roles = RoleRepo::listRole(['max_level'=>UserAuth::user('level')]);
            $this->output['data']['roles'] = $roles['data'];
        }
        return $this->done();
    }

    /**
     * POST /api/user/
     * 
     * @param Request $request 
     *      name
     *      email
     *      username
     *      password
     *      role_code
     */
    public function create(Request $request)
    {        
        $this->response = redirect()->route('user.list');

        $userData = $request->all();//$request->only(['name', 'email', 'password']);

        $validator = [
            'name' => 'required|min:3|max:255',
            'password' => 'required|min:5|max:255'
        ];

        if(isset($userData['username']) && $userData['username']!=''){
            $validator['username'] = 'required|min:3|max:255';
        }

        if(isset($userData['email']) && $userData['email']!=''){
            $validator['email'] = 'required|email|max:255';
        }

        if(!isset($validator['username']) && !isset($validator['email'])){
            $this->setError('Username atau email harus diisi');
            return $this->done();
        }

        $validator = \Validator::make($userData, $validator);

        if ($validator->fails()) {       
            $this->setError(__('validation.inputerror'),$validator->messages());
            return $this->done();
        }
        
        $this->output['data']['instansi'] = Master::listInstansiPerParent();

        //jika berhasil
        if ($user = UserRepo::register($userData,false)) {
            $this->setAlert('Data Inserted successfully','success');
        }else{
            $this->setError(UserRepo::error(),'success');
        }
        $this->output['data']['instansiList'] = Master::listInstansi();
        
        return $this->done();
    }

    /**
     * PUT - Update user
     */
    public function update(Request $request)
    {
        $this->response = redirect()->route('user.list');

        $id = $request->route('id');

        $input = $request->all();

        $validator = [];

        if(isset($input['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($input['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($input['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }

        if(array_key_exists('banned_note',$input) && $input['banned_note'] == null){
            unset($input['banned_note']);
        }

        if(UserRepo::updateUser($id, $input)) {            
            $this->setAlert('Data Updated successfully','success');
        }else{
            $this->setAlert(UserRepo::error(),'danger');
            $this->setError(UserRepo::error());
        }

        return $this->done();
    }
    

    
    /**
     * update password
     * 
     * @param Request $request
     *      password
     *      password_confirmatin
     */
    public function updatePassword(Request $request)
    {
        $this->response = redirect()->route('user.list');
        $id = $request->route('id');
        $userData = $request->only(['password','password_confirmation']);

        $validator = \Validator::make($userData, [
            'password' => 'required|min:8|max:255',
            'password_confirmation' => 'required|min:8|max:255|same:password'
        ]);

        if ($validator->fails()) {
            $this->setError('Input Error :',$validator->messages(),400,true);
            return $this->done();
        }

        $change = UserRepo::updateUser($id, $userData);
        if (!$change) {
            return $this->done();
        }

        $this->setAlert('Password Updated successfully','success');
        return $this->done();
    }

    public function ban(Request $request)
    {
        $this->response = redirect()->route('user.list');
        $id = $request->route('id');
        UserRepo::banUser($id,$request->input('banned_note',''));
        $this->setAlert('User banned successfully','success');
        return $this->done();
    }

    public function unban(Request $request)
    {
        $this->response = redirect()->route('user.list');
        $id = $request->route('id');
        UserRepo::unbanUser($id);
        $this->setAlert('User unbanned successfully','success');
        return $this->done();
    }

    public function delete(Request $request)
    {

        $this->response = redirect()->route('user.list');
        $id = $request->route('id');
           
        if(UserAuth::isLogin() && $id != UserAuth::user('id')){
            $filter[] = ['id', $id];
            $filter[] = ['level','>',UserAuth::user('level')];
            $data = UserRepo::listUser($filter);
            if($data['count']<=0){
                $this->setError('Permission denied');
                return $this->done();;
            }
        }   

        if(!UserRepo::deleteUser($id)){
            $this->setError('Error : '.UserRepo::error());
        }
        return $this->done();
           
    }

    /**
     * PROFILE
     * =================================================================
     */

    /**
     * GET /api/profile
     * 
     */
    public function profile(Request $request)
    {
        $this->response = 'user.profile';

        $this->output['data'] = UserAuth::user();
        $this->output['data']['user_role'] = UserRepo::getUserRole($this->output['data']['id']);

        foreach($this->output['data']['user_role'] as $key => $val) {
            if($val['is_main_role']){
                $this->output['data']['role_code'] = $key;
            }
        } 

        return $this->done();
    }
    
    public function updateProfile(Request $request)
    {
        
        $this->response = redirect()->route('user.list');
        if(UserAuth::isLogin()){
            $id = UserAuth::user('id');
        }else{
            $this->setError('User belum login');
            return $this->done();
        }

        $input = $request->all();

        if(isset($input['id']))unset($input['id']);
        if(isset($input['created_at']))unset($input['created_at']);
        if(isset($input['updated_at']))unset($input['updated_at']);

        $validator = [];

        if(isset($input['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($input['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($input['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }
        
        if(isset($input['role_code']))unset($input['role_code']);
        if(isset($input['status']))unset($input['status']);
        
        if(UserRepo::updateUser($id, $input)) {            
            $this->setAlert('Data Updated successfully','success');
        }else{
            $this->setAlert(UserRepo::error(),'danger');
            $this->setError(UserRepo::error());
        }

        return $this->done();
    }
    
    public function changeRole(Request $request)
    {
        $roleCode = $request->route('role_code');
        $backLink = $request->input('backlink',false);
        $this->response = $backLink?redirect($backLink):back();

        if(UserAuth::setActiveRole($roleCode)){
            $roleName = UserAuth::role($roleCode)['name'];
            $this->setAlert('Role <b>'.$roleName.'</b> berhasil diaktifkan','success');

        }else{
            $this->setAlert('Role tidak ditemukan','danger');
        }

        return $this->done();
    }
}
