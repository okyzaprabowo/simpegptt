<?php

namespace App\MainApp\Modules\Pegawai\Controllers;

use Illuminate\Http\Request;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;
use Facades\App\MainApp\Repositories\Kepegawaian;
use Facades\App\MainApp\Repositories\Master;
use Facades\App\MainApp\Repositories\Absensi;
use App\Base\BaseController;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends BaseController
{
    public function __construct()
    {
        // $this->forceApiOutput();
    }

    /**
     * list pegawai
     */
    public function index(Request $request)
    {
        if(!UserAuth::hasAccess('Pegawai.master')){
            $this->response = redirect()->route('dashboard');
            $this->setAlert('Akses ditolak','danger'); 
            return $this->done();
        }
        
        $this->response = 'pegawai.list';

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

        if(UserAuth::getActiveUserRoleCode()=='pejabat_approval'){
            
            $instansiId = UserAuth::user('profile')['instansi_id'];

        }else if(UserAuth::getActiveUserRoleCode()=='admin_satker' || UserAuth::getActiveUserRoleCode()=='pimpinan2'){
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                //$instansiId[1] = eselon 2, jika tidak ada berarti eselon 2 adalah instansi yg dipilih
                if(isset($instansiId[1])){
                    $instansiId = $instansiId[1];
                }else{
                    $instansiId = UserAuth::user('profile')['instansi_id'];
                }
            }
        }else if(UserAuth::getActiveUserRoleCode()=='pimpinan3') {
            $instansiId=0;
            if(UserAuth::user('profile')['instansi_induk_path']){
                $instansiId = explode(';',trim(UserAuth::user('profile')['instansi_induk_path'],';'));
                //$instansiId[2] == eselon 3, jika tidak ada berarti eselon 3 adalah instansi yg dipilih
                if(isset($instansiId[2])){
                    $instansiId = $instansiId[2];
                }else{
                    $instansiId = UserAuth::user('profile')['instansi_id'];
                }
            }
        }

        if(isset($instansiId)){
            $filter[] = [['instansi_id', $instansiId],['OR instansi_induk_path', 'LIKE', '%;'.$instansiId.';%']];
        }

        $this->output['data'] = Kepegawaian::listPegawai(
            $filter, $limit['offset'], $limit['limit'],$orderBy
        );

        //generate pagination
        $this->output['viewdata']['pagination'] = Kepegawaian::getPaginationPegawai(route('pegawai.list', $paginationParams));
        $this->output['viewdata']['filter'] = $paginationParams;
        
        return $this->done();
    }
    

    /**
     * GET - form add new
     */
    public function addNew(Request $request)
    {
        $this->response = 'pegawai.form';
        
        $this->output['data']['mode'] = 'add';
        $this->output['data']['data'] = [];
        $this->output['data']['count'] = 0;
        // $this->output['data']['instansi'] = Master::listInstansiPerParent();
        $this->output['data']['jabatan'] = Master::listJabatan();

        $this->output['data']['instansiIds'] = [1];
        $this->output['data']['instansiList'] = Master::listInstansi();

        return $this->done();
    }

    /**
     * POST - save data
     */
    public function create(Request $request)
    {
        $pegawaiData = $request->except(['user']);
        $userData = $request->only(['nama', 'email', 'user']);
        if(isset($userData['user'])){
            $userData['username'] = $userData['user']['username'];
            $userData['password'] = $userData['user']['password'];
            unset($userData['user']);
        }

        $validator = [
            'nama' => 'required|min:3|max:255',
            'password' => 'required|min:5|max:255'
        ];

        if(isset($userData['email']) && $userData['email']){
            $validator['email'] = 'email|max:255';
        }else{
            if(isset($userData['email'])) unset($userData['email']);
        }

        if(isset($userData['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        $validator = \Validator::make($userData, $validator);

        $userData['role_code'] = 'pegawai_ptt';

        if ($validator->fails()) {       
            $this->setError(__('validation.inputerror'),$validator->messages());
            return $this->done();
        }

        $userData['name'] = $userData['nama'];
        unset($userData['nama']);

        if($request->file('foto',null)){
            $pegawaiData['foto'] = $request->file('foto')->store('images/photo'); 
            $userData['avatar'] = $pegawaiData['foto'];      
        }else{
            $userData['avatar'] = $pegawaiData['foto'] = '';
        }

        //jika berhasil
        if ($user = UserRepo::register($userData,false)) {   
            $pegawaiData['user_id'] = $user['id'];      
            Kepegawaian::createPegawai($pegawaiData);   
            $this->setAlert('Data Inserted Successfully');
        }else{
            //jika sudah upload ttd maka delete
            if($pegawaiData['foto']!=''){
                \Illuminate\Support\Facades\Storage::delete($pegawaiData['foto']);
            }
            $this->setError(UserRepo::error());
        }        
        
        return $this->done();
    }

    public function edit(Request $request)
    {
        $this->response = 'pegawai.form';
        $id = $request->route('id');

        $this->output['data']['data'] = Kepegawaian::getPegawai($id);
        if(!$this->output['data']['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('pegawai.list');
        }
        if($this->output['data']['data']['foto'])$this->output['data']['data']['foto_url'] = Storage::url($this->output['data']['data']['foto']);
        
        $this->output['data']['mode'] = 'edit';
        $this->output['data']['jabatan'] = Master::listJabatan();

        $this->output['data']['instansiIds'] = Master::getInstansi($this->output['data']['data']['instansi_id']);
        $this->output['data']['instansiIds'] = explode(';',trim($this->output['data']['instansiIds']['induk_path'],';'));
        $this->output['data']['instansiIds'][] = $this->output['data']['data']['instansi_id'];
        
        if(!$this->output['data']['data']['instansi_id'] || !$this->output['data']['instansiIds'][0])
            $this->output['data']['instansiIds'] = [1];
        
        $this->output['data']['instansiList'] = Master::listInstansi();

        // $this->output['data']['instansi'] = Master::listInstansiPerParent();
        // $this->output['data']['instansi'] = [];
        // foreach ($this->output['data']['instansiIds'] as $value) {
        //     if(!$value)$value=0;
        //     $this->output['data']['instansi'][] = Master::listInstansi(['induk',$value]);
        // }
        return $this->done();
    }

    public function view(Request $request)
    {
        $this->response = 'pegawai.form';
        $id = $request->route('id');

        $this->output['data']['data'] = Kepegawaian::getPegawai($id);
        if($this->output['data']['data']['foto'])$this->output['data']['data']['foto_url'] = Storage::url($this->output['data']['data']['foto']);

        if(!$this->output['data']['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('pegawai.list');
        }
        
        $this->output['data']['mode'] = 'view';
        $this->output['data']['jabatan'] = [];

        $this->output['data']['instansiIds'] = Master::getInstansi($this->output['data']['data']['instansi_id']);
        $this->output['data']['instansiIds'] = explode(';',trim($this->output['data']['instansiIds']['induk_path'],';'));
        $this->output['data']['instansiIds'][] = $this->output['data']['data']['instansi_id'];        
        if(!$this->output['data']['data']['instansi_id'] || !$this->output['data']['instansiIds'][0])
            $this->output['data']['instansiIds'] = [1];

        $this->output['data']['instansiList'] = Master::listInstansi();

        return $this->done();
    }

    public function update(Request $request)
    {
        $id = $request->route('id');

        $input = $request->all();

        $pegawaiData = $request->except(['password','username']);
        $userData = $request->only(['nama', 'email', 'user', 'phone']);
        if(isset($userData['user'])){
            $userData['username'] = $userData['user']['username'];
            $userData['password'] = $userData['user']['password'];
            unset($userData['user']);
        }
        if(isset($userData['nama'])){
            $userData['name'] = $userData['nama'];
            unset($userData['nama']);
        }
        $validator = [];

        if(isset($userData['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($userData['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($userData['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($userData['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($userData, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }

        $deleteFoto = false;
        if($request->file('foto',null)){
            $userData['avatar'] = $pegawaiData['foto'] = $request->file('foto')->store('images/photo'); 
        }else{
            if(array_key_exists('foto',$pegawaiData)){
                unset($pegawaiData['foto']);            
            }else{
                $userData['avatar'] = $pegawaiData['foto'] = '';
                $deleteFoto = true;
            }
            
            // if(array_key_exists('avatar',$userData)){
            //     unset($userData['avatar']);            
            // }
        }

        $pegawaiOld = Kepegawaian::getPegawai(['id',$id]);
        // $oldData = UserRepo::getUser($pegawaiOld['user_id']);

        if(UserRepo::updateUser($pegawaiOld['user_id'], $userData)) {
            //jika foto diubah dan sebelumnya ada foto, makah hapus foto sebelumnya
            if((isset($pegawaiData['foto']) && $pegawaiOld['foto']!='' && $pegawaiData['foto']) || $deleteFoto){
                \Illuminate\Support\Facades\Storage::delete($pegawaiOld['foto']);
            } 
            Kepegawaian::updatePegawai([['id',$id]], $pegawaiData); 

            $this->setAlert('Data Updated successfully','success');
        }else{
            //jika melakukan update ttd, maka hapus
            if(array_key_exists('foto',$pegawaiData) && $pegawaiData['foto']!=''){
                \Illuminate\Support\Facades\Storage::delete($pegawaiData['foto']);
            }
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
        $this->response = redirect()->route('pegawai.list');

        $id = $request->route('id');
        $userData = $request->all();

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

    public function suspend(Request $request)
    {
        $id = $request->route('id');

        UserRepo::suspendUser($id);
        return $this->done();
    }

    public function delete(Request $request)
    {
        $this->response = redirect()->route('pegawai.list');
        $id = $request->route('id');
           
        $pegawaiOld = Kepegawaian::getPegawai(['id',$id]);
        $user_id = isset($pegawaiOld['user_id'])?$pegawaiOld['user_id']:0;
        $oldData = UserRepo::getUser($user_id);

        if($oldData && UserAuth::isLogin() && $id != UserAuth::user('id')){
            $filter[] = ['id', $user_id];
            $filter[] = ['level','>',UserAuth::user('level')];
            $data = UserRepo::listUser($filter);
            if($data['count']<=0){
                $this->setError('Permission denied');
                return $this->done();
            }
        }

        if($pegawaiOld||$oldData){            
            UserRepo::deleteUser($user_id);
            Kepegawaian::deletePegawai($id);
            $this->output['message'] = 'Pegawai berhasil dihapus';
        }else{
            $this->setError('Error : Pegawai tidak ditemukan');
        }

        return $this->done();
    }
        
    public function profile(Request $request)
    {
        $this->response = 'pegawai.form';
        
        $this->output['data']['data'] = Kepegawaian::getPegawai(['user_id',UserAuth::user('id')]);

        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('pegawai.list');
        }
        if($this->output['data']['data']['foto'])$this->output['data']['data']['foto_url'] = Storage::url($this->output['data']['data']['foto']);
        $this->output['data']['mode'] = 'profile';
        // $this->output['data']['instansi'] = [];
        // $this->output['data']['instansi'] = Master::listInstansiPerParent();
        $this->output['data']['instansiIds'] = Master::getInstansi($this->output['data']['data']['instansi_id']);
        $this->output['data']['instansiIds'] = explode(';',trim($this->output['data']['instansiIds']['induk_path'],';'));
        $this->output['data']['instansiIds'][] = $this->output['data']['data']['instansi_id'];        
        if(!$this->output['data']['data']['instansi_id'] || !$this->output['data']['instansiIds'][0])
            $this->output['data']['instansiIds'] = [1];
        
        $this->output['data']['instansiList'] = Master::listInstansi();

        return $this->done();
    }
        
    public function updateProfile(Request $request)
    {
        $this->response = 'pegawai.form';
        $this->output['data']['data'] = Kepegawaian::getPegawai(['user_id',UserAuth::user('id')]);

        if(!$this->output['data']){
            $this->setError('Data Not Found');
            //jika error redirect ke halaman list
            $this->response = redirect()->route('dashboard');
        }

        return $this->done();
    }
}
