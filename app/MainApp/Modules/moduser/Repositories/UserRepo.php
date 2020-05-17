<?php

namespace App\MainApp\Modules\moduser\Repositories;

use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Facades\App\MainApp\Modules\moduser\Repositories\UserLogRepo;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

//use semua model yg diperlukan
use App\MainApp\Models\Instansi;
use App\MainApp\Modules\moduser\Models\User;
use App\MainApp\Modules\moduser\Models\UserProfile;
use App\MainApp\Modules\moduser\Models\PasswordReset;
use App\MainApp\Modules\moduser\Models\UserRole;
use App\MainApp\Modules\moduser\Models\Role;
// use App\MainApp\Modules\moduser\Models\ApiToken;

use App\MainApp\Modules\moduser\Models\UserTenant;
use App\Models\Tenant;

use Validator;
use Mail;
use App\Base\BaseRepository;
use Exception;

class UserRepo extends BaseRepository
{
    use ApiTokenTraits, UserMessageTraits;


    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     *  true / false operation method
     * ==========================================================================
     */

    /**
     * 
     * @param string $key
     * @param type $value
     * @return boolean : true jika ada, false jiak tidak ada
     */
    public function isUserExist($key, $value = NULL)
    {
        //jika tidak menyertakan value berarti default nya by apps code
        if (is_null($value)) {
            $value = $key;
            $key = 'id';
        }

        return $this->model->where($key, $value)->exists();
    }

    /**
     * cek apakah user tertentu memiliki role tertentu
     * 
     * @param integer $userId user id
     * @param string $roleCode 
     */
    public function isHasRole($userId, $roleCode)
    {
        $role = Role::where('role_code', $roleCode)->first();
        if ($role)
            return User::where('id', $userId)->where('role', 'LIKE', '%;' . $roleCode . ';%')->exists();
    }

    /**
     * get user berdasarkan username/email dan password nya
     * 
     * @param text $username
     * @param text $password
     * @param integer|null $tenantId id tenant, atau null jika auto detect
     * 
     * @return false|array false jika user tidak ditemukan, atau jika berhasil array :
     *      * record table user
     *      profile :
     *          * record table user_profile
     *      tenant : * jika auto detect tenant, tenant utama/defaul, false jika tidak terdaftar di tenant manapun
     */
    public function loginCheck($username, $password, $tenantId = null)
    {
        $userData = User::where('username', $username)->first();

        if ($userData == null) {
            $userData = User::where('email', $username)->first();
            if ($userData == null) {
                $this->error = 'Username or email not found';
                return false;
            }
        }

        if (!Hash::check($password, $userData->password)){
            $this->error = 'Password not match';
            return false;
        }

        $user = $userData->toArray();

        //user banned
        if($user['status']==2){
            $this->error = 'Account banned';
            return false;
        }

        //jika multitenant aktif dan user tidak all_tenant
        if (config('AppConfig.system.web_admin.multitenant.active')==1 && $user['all_tenant']==0) {

            //jika null berarti autodetect tenant
            if (is_null($tenantId)) {
                $user['tenant'] = UserTenant::with('tenant')->where('user_id',$user['id']);
                if($user['tenant']->count()==0){
                    $user['tenant'] = false;
                }else{
                    $user['tenant'] = $user['tenant']->first()->toArray();
                    $user['tenant'] = $user['tenant']['tenant'];
                }
            //jika tidak punya akses all tenant maka cek tenant
            } else if ($user['all_tenant']==0) {
                if (UserTenant::where('tenant_id', $tenantId)->where('user_id',$user['id'])->first() == null) {
                    $this->error = 'Account not registered on tenant : '.$tenantId;
                    return false;
                }
            }
        }

        $user['profile'] = $userData->profile ? $userData->profile->toArray() : [];
        return $user;
    }

    /**
     * cek apakah email sudah terdaftar sebelumnya
     * 
     * @param type $email
     * @param type $except
     * @return boolean
     */
    public function isEmailRegistered($email, $except_user_id = false)
    {
        $user = User::where('email', $email);

        if ($except_user_id) {
            $user = $user->where('id', '!=', $except_user_id);
        }

        if ($user->exists()) {
            return true;
        }

        return false;
    }

    /**
     * cek apakah phone sudah terdaftar sebelumnya
     * 
     * @param string $phone
     * @param type $except
     * @return boolean
     */
    public function isPhoneRegistered($phone, $except_user_id = false)
    {
        $user = User::where('phone', $phone);

        if ($except_user_id) {
            $user = $user->where('id', '!=', $except_user_id);
        }

        if ($user->exists()) {
            return true;
        }

        return false;
    }
    
    /**
     * cek apakah username sudah terdaftar sebelumnya
     * 
     * @param string $username
     * @param type $except
     * @return boolean
     */
    public function isUsernameRegistered($username, $except_user_id = false)
    {
        $user = User::where('username', $username);

        if ($except_user_id) {
            $user = $user->where('id', '!=', $except_user_id);
        }

        if ($user->exists()) {
            return true;
        }

        return false;
    }

    /**
     *  getter operation method
     * ==========================================================================
     */

     /**
      * 
      * @param $filter array
      *     profile
      *     tenant
      */
    public function listUser($filter = false, $offset = 0, $limit = 0,$orderBy=false)
    {
        if (!$filter) $filter = [];
        $filter['searchField'] = ['name','email','username'];
        $filter['hiddenColumn'] = ['created_at', 'updated_at', 'cached_at'];

        $user = User::with(['profile','roles.role']);

        if(isset($filter['profile'])){
            $user->whereHas('profile', function($q) use ($filter){
                $q = $this->_where($q,$filter['profile']);
            });
            unset($filter['profile']);
        }

        if(isset($filter['tenant'])){
            // $user = $user->where('all_tenant');
            $user->whereHas('userTenant', function($q) use ($filter){
                $q = $this->_where($q,[['tenant_id',$filter['tenant']]]);
            });
            unset($filter['tenant']);

        }
        
        $data = $this->_list(
            $user,
            $filter,
            $offset,
            $limit,
            $orderBy
        );
        $this->dataUserPagination = $this->pagination;

        $data['data'] = array_map([$this,'_formatUser'],$data['data']);

        return $data;
    }

    public function getPaginationUser($path = '')
    {
        if(!isset($this->dataUserPagination))$this->dataUserPagination = $this->pagination;
        return $this->_getPagination($path, $this->dataUserPagination);
    }

    /**
     * get 1 record user beserta profile nya
     * 
     * @param array|integer     $userId
     * @return array|false      jika tidak ada
     */
    public function getUser($userId)
    {
        $user = User::with(['profile','roles.role']);

        if(is_array($userId)){
            if(count($userId)==2){
                $user = $user->where($userId[0],$userId[1])->first();
            }else{                
                $user = $user->where($userId[0],$userId[1],$userId[2])->first();
            }
        }else{
            $user = $user->find($userId);
        }
        
        if ($user) {          
            return $this->_formatUser($user->toArray());
        }
        return false;
    }

    /**
     * Hanya digunakan untuk fungsi yang memerlukan Model user, misal auth, notifikasi, dll
     * selain itu tidak boleh.
     * 
     * Digunakan untuk ambil 1 record user berbentuk model elequent, biasanya
     * digunakan untuk keperluan fitur-fitur yg berkaitan dengan user model.
     * 
     * @param type $user_id
     * @return type
     */
    public function getUserModel($user_id)
    {
        return User::find($user_id);
    }

    public function getOneBySocnetId($id, $provider)
    {
        $userData = User::with(['profile'])->where('socialauth_' . $provider . '_id', $id)->first();

        if (!$userData) return false;
        return $this->_formatUser($userData->toArray());
    }

    public function getOneBySocnetIdOrEmail($id, $email, $provider)
    {
        $userData = User::with(['profile'])->where('email', $email)
            ->orWhere('socialauth_' . $provider . '_id', $id)
            ->first();

        if (!$userData) return false;
        return $this->_formatUser($userData->toArray());
    }

    /**
     * helper getter untuk get data user
     */
    private function _filterUserResult(array $result)
    {
        $hiddenField = array_merge(
            config('AppConfig.packageLocal.moduser.users_hidden_field'), 
            config('AppConfig.packageLocal.moduser.user_profiles.hide')
        );

        return $this->_filterField($result, $hiddenField);
    }

    /**
     * helper getter untuk get data user
     * fomat data user sesuai keperluan
     */
    private function _formatUser(array $user) {
        $user = $this->_filterField($user, config('AppConfig.packageLocal.moduser.users_hidden_field'));

        if (isset($user['avatar']) && $user['avatar'])
            $user['avatar_url'] = Storage::url($user['avatar']);

        if (!empty($user['profile'])) {
            $user['profile'] = $this->_filterField($user['profile'], config('AppConfig.packageLocal.moduser.user_profiles.hide'));
            unset($user['profile']['id'], $user['profile']['user_id'],
                $user['profile']['created_at'], $user['profile']['updated_at']);
                
            // $user = array_merge($user, $user['profile']); // splice in at position 3
        }
        // unset($user['profile']);        
        return $user;
    }

    /**
     *  setter operation method
     * ==========================================================================
     */

    /**
     * rigistrasi user baru
     * 
     * @param array $userData : seluruh field di table user dan :
     *      all_tenant : jika tidak disertakan maka dianggap per tenant
     *      role_code : * optional    string role_code, jika tidak dicantumkan akan menggunakan default role_code
     * 
     * @param boolean $generateToken 1 jika generate token, 0 jika tidak
     * 
     * @return array : seluruh field di table user dan :
     *      token : api_token dari table api_tokens yg digenerate saat registrasi, jika generatetoken true
     *      token_id : id dari table api_tokens nhya
     *      
     */
    public function register(array $userData, $generateToken = true)
    {
        $userData = $this->registerFilter($userData);

        if (!$userData) {
            return false;
        }

        $userData['password'] = isset($userData['password']) ? Hash::make($userData['password']) : '';

        $userData['user_idcode'] = $this->generateUserIdcode();
        // if(!isset($userData['tenant_id']))$userData['tenant_id'] = config('tenant.id') ? config('tenant.id') : 0;//jika 0 berarti tanpa tenant atau bisa akses semua tenant

        //role dikosongin dahulu karena insert role di proses selanjutnya
        $role = $userData['role_code'];
        // $mainRole = $userData['main_role'];
        unset($userData['role_code']);//,$userData['main_role']);

        $data = $this->model->create($userData);
        $data = $data->toArray();
        try {
            //jika menyertakan tenant maka daftarkan user di tenant bersangkutan
            if(!(isset($userData['all_tenant']) && $userData['all_tenant']==1)){
                if(config('tenant.id')){
                    UserTenant::create([
                        'tenant_id' => config('tenant.id'),
                        'user_id' => $data['id']
                    ]);
                    $userData['all_tenant'] = 0;
                }
            }

            if ($generateToken) {
                $apiTokenData = $this->generateToken($data['id']);
                $data['token'] = $apiTokenData['api_token'];
            }

            /**
             * add profile
             */
            $userProfile = [];
            if(isset($userData['profile'])) $userProfile = $userData['profile'];            
            $userProfile['user_id'] = $data['id'];
            $this->registerProfile($userProfile);

            //update role data          
            $this->updateUserRole($data['id'],$role);
            // $this->addUserRole(
            //     $data['id'],
            //     $role['role_code'],
            //     1,
            //     $role['has_auth_grant'],
            //     false
            // );

            if (isset($userData['email']))
                $this->sendUserActivationEmail($data['id']);
        } catch(Exception $e) {
            $this->model->where('id',$data['id'])->delete();
            $this->error = $e->getMessage();
            return false;
        }

        return $data;
    }

    public function registerFilter(array $userData)
    {
        $validatorRule = [
            'name' => 'required|min:5|max:255',
        ];
        if (isset($userData['password'])) {
            $validatorRule['password'] = 'required|min:5|max:255';
        }
        if (isset($userData['email']) && $userData['email']) {
            $validatorRule['email'] = 'required|email|min:5|max:255';
        }else{
            $userData['email'] = '';
        }
        if (isset($userData['username'])) {
            $userData['username'] = str_replace(' ','',$userData['username']);
            $validatorRule['username'] = 'required|min:5|max:255';
        }
        $validator = Validator::make($userData, $validatorRule);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $err[] = '<ul>';
            foreach ($errors->all() as $message) {
                $err[] = '<li>' . $message . '</li>';
            }
            $err[] = '</ul>';
            $this->error = implode('', $err);
            return false;
        }

        if (isset($userData['phone'])) {
            $userData['phone'] = $this->phoneFormat($userData['phone']);
        }

        //jika tidak menyertakan role_id maka set default
        if (!isset($userData['role_code'])) {
            if(config('AppConfig.system.web_admin.registration.default_role_code')){
                $userData['role_code'] = [config('AppConfig.packageLocal.moduser.registration.default_role_code')];
            }else{
                $userData['role_code'] = [config('AppConfig.packageLocal.moduser.registration.default_role_code')];
            }            
        }
        if(!is_array($userData['role_code']))$userData['role_code'] = [$userData['role_code']];

        //pastikan rule nya ada        
        $roles = Role::whereIn('role_code', $userData['role_code'])->orderBy('level','ASC')->get();
        if (!$roles) {
            $this->error = 'Role not defined.';
            return false;
        }
        $userData['role_code'] = [];
        $isFirstRow=true;
        foreach ($roles as $val) {  
            $userData['role_code'][] = $val->role_code;
            if($isFirstRow){
                $userData['main_role'] = ['role_code'=>$val->role_code,'level'=>$val->level];
                $isFirstRow=false;
            }
        }

        $userData['level'] = $userData['main_role']['level'];

        if (isset($userData['email']) && $userData['email'] && $this->isEmailRegistered($userData['email'])) {
            $this->error = 'Email already registered.';
            return false;
        }

        if (isset($userData['phone']) && $this->isPhoneRegistered($userData['phone'])) {
            $this->error = 'Phone already registered.';
            return false;
        }
        if (isset($userData['username']) && $this->isUsernameRegistered($userData['username'])) {
            $this->error = 'Username already registered.';
            return false;
        }
        
        //pastikan tidak ada parameter yang ksosong kecuali email
        foreach ($userData as $key => $value) {
            if(empty($value) && $key!='email')unset($userData[$key]);
        }

        return $userData;
    }

    /**
     * 
     * @param array $userData gabungan data users & user_profile
     */
    public function registerProfile(array $userData)
    {
        $userData = $this->registerProfileFilter($userData);
        if (!$userData) {
            return false;
        }//jika pindah instansi
        if(isset($userData['instansi_id'])){
            if($instansi = Instansi::where('id',$userData['instansi_id'])->first()){
                $userData['instansi_induk_path'] = $instansi->induk_path;
            }            
        }
        UserProfile::create($userData);
    }

    /**
     * 
     * @param array $userData
     * @return boolean
     */
    public function registerProfileFilter(array $userData)
    {
        
        $validatorRule = [
            'user_id' => 'required',
        ];
        $validator = Validator::make($userData, $validatorRule);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $err[] = '<ul>';
            foreach ($errors->all() as $message) {
                $err[] = '<li>' . $message . '</li>';
            }
            $err[] = '</ul>';
            $this->error = implode('', $err);
            return false;
        }
        
        //pastikan tidak ada parameter yang ksosong
        foreach ($userData as $key => $value) {
            if(empty($value) || is_null($value)) unset($userData[$key]);
        }
        return $userData;
    }

    public function deleteUser($userId){
        $oldUser = $this->_getOne(new User,[['id',$userId]]);
        
        if(!$oldUser){
            $this->error = 'user tidak ditemukan';
            return false;
        }
        
        if($oldUser['avatar']!=''){
            \Illuminate\Support\Facades\Storage::delete($oldUser['avatar']);
        }

        $this->_delete(new User,[['id',$userId]]);
        $this->_delete(new UserProfile,[['user_id',$userId]]);
        $this->_delete(new UserRole,[['user_id',$userId]]);
        return true;
    }
    /**
     * 
     * @param integer           $userId user id user yang akan diupdate
     * @param array             $userData
     *      role_code *optional string role_code, jika disertakan maka akan mengubah role utama
     * 
     * @return boolean
     */
    public function updateUser($userId, $userData)
    {
        
        if (isset($userData['_token'])) unset($userData['_token']);
        if (isset($userData['_method'])) unset($userData['_method']);
        if (isset($userData['password']) && $userData['password']) $userData['password'] = Hash::make($userData['password']);

        if (isset($userData['username']) && $this->isUsernameRegistered($userData['username'], $userId)){
            $this->error = 'Username already registered.';
            return false;
        }
        
        if (isset($userData['email']) && $userData['email'] && $this->isEmailRegistered($userData['email'], $userId)) {
            $this->error = 'Email already registered.';
            return false;
        }

        if (isset($userData['phone']) && $userData['phone'] && $this->isPhoneRegistered($userData['phone'], $userId)) {
            $this->error = 'Phone already registered.';
            return false;
        }
        
        //jika menyertakan profile, maka proses update table profile
        if(isset($userData['profile'])){
            $this->updateProfile($userId, $userData['profile']);
            unset($userData['profile']);
        }
        
        //pastikan tidak ada parameter yang kosong
        foreach ($userData as $key => $value) {
            if(empty($value))unset($userData[$key]);
        }
        
        //upload avatar jika menyertakan avatar
        if (isset($userData['avatar']) && !empty($userData['avatar']) && !is_string($userData['avatar'])) {
            $userData['avatar'] = Storage::putFile('images/avatar', $userData['avatar']);
            
            $userTmp = $this->_getOne(new User, $userId);

            if (!empty($userTmp['avatar'])) {
                Storage::delete($userTmp['avatar']);
            }
        }

        if(isset($userData['email']))$this->updateEmail($userId,$userData);
        if(isset($userData['phone']))$this->updatePhone($userId,$userData);
        
        //jika tidak menyertakan role_id maka set default
        if (isset($userData['role_code'])){            
            $this->updateUserRole($userId,$userData['role_code']);
            unset($userData['role_code']);
        }

        $this->_update(new User, $userId, $userData);

        return true;
    }


    public function banUser($id,$banNote='')
    {
        return User::find($id)->update(['status' => 2,'banned_note'=>$banNote,'banned_at'=>now()]);
    }

    public function unbanUser($id)
    {
        return User::find($id)->update(['status' => 1]);
    }

    public function updateProfile($userId, $userData)
    {
        $userProfile = $this->_getOne(new User, ['id',$userId]);
        if (!$userProfile) {
            $this->error = 'User tidak ditemukan';
            return false;
        }

        $model = new UserProfile;
        $input = [];
        $userProfileField = Schema::getColumnListing($model->getTable());
        
        foreach ($userProfileField as $value) {
            if (!in_array($value,['id','created_at','updated_at','user_id']) && isset($userData[$value]))
                $input[$value] = $userData[$value];
        }

        if($input!=[]){
            
            //jika pindah instansi
            if(isset($input['instansi_id'])){
                if($instansi = Instansi::where('id',$input['instansi_id'])->first()){
                    $input['instansi_induk_path'] = $instansi->induk_path;
                }            
            }

            if($this->_exists($model, ['user_id',$userId])){
                $this->_update($model, ['user_id',$userId], $input);
            }else{
                $input['user_id'] = $userId;
                $this->_create($model, $input);
            }            
        }
    }

    public function updatePhone($userId, $userData)
    {
        if (isset($userData['phone'])) {
            $input = User::find($userId);
            $input->phone = $this->phoneFormat($userData['phone']);
            $input->phone_verified_at = null;
            $input->update();
        }

        return true;
    }

    public function updateEmail($userId, $userData)
    {
        if (isset($userData['email'])) {
            $input = User::find($userId);
            $input->email = $userData['email'];
            $input->email_verified_at = null;
            $input->update();
        }
        return true;
    }

    /**
     * reset password user
     * @param type $userId
     * @param type $newPassword
     */
    public function resetPassword($userId, $newPassword)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->error = __('User tidak ditemukan');
            return false;
        }
        $user->password = Hash::make($newPassword);
        $user->save();
        return true;
    }

    public function activateUser($user_id)
    {
        $userData = $this->model->find($user_id);

        if (!$userData) {
            $this->error = 'User not found.';
            return false;
        }

        UserLogRepo::addActivityLog($user_id, 'activate');

        return $userData->update(['status' => 1]);
    }

    /**
     * do email verification
     * 
     * @param type $email
     * @param type $verifyCode
     * @return boolean
     */
    public function varifyEmail($email, $verifyCode)
    {
        if ($this->generateEmailVerfifyCode($email) == $verifyCode) {
            $user = User::where('email', $email)->whereNull('email_verified_at')->first();
            if (!$user) {
                $this->error = __('auth.emailverify_fail_mailnotfound');
                return false;
            }
            $user->{'email_verified_at'} = now()->toDateTimeString();
            $user->save();
            return true;
        }
        $this->error = __('auth.emailverify_fail_varificationcodeinvalid');
        return false;
    }

    /**
     * do phone verification with OTP
     * 
     * @param type $phone
     * @param type $otpCode
     * @return boolean
     */
    public function varifyPhone($phone, $otpCode)
    {
        if ($this->isOTPValid($phone, $otpCode)) {
            $phoneField = 'phone';
            $user = User::where('phone', $phone)->whereNull('phone_verified_at')->first();
            if (!$user) {
                $this->error = __('auth.phoneverify_fail_phonenotfound');
                return false;
            }
            $user->{'phone_verified_at'} = now()->toDateTimeString();
            $user->save();
            return true;
        }
        $this->error = __('auth.phoneverify_fail_verificationcodeinvalid');
        return false;
    }

    public function varifyResetPasswordToken($email, $verifyCode, $delete = false)
    {
        //jika match
        if ($this->generateEmailVerfifyCode($email) == $verifyCode) {

            $passwordReset = PasswordReset::where('email', $email)->where('token', $verifyCode)->first();
            if (!$passwordReset) {
                $this->error = __('auth.resetpassword_fail_mailnotfound');
                return false;
            }
            if ($delete) $passwordReset->delete();
            return true;
        }
        $this->error = __('auth.resetpassword_fail_verificationcodeinvalid');
        return false;
    }



    /**
     * general helper
     * =======================================================================
     */

    
    /**
     * update format nomor telepon menja
     * @param type $phone
     * @return string
     */
    public function phoneFormat($phone)
    {
        $phone = ltrim($phone, '0');
        //jika belum memasukan kode negara maka set indonesia
        if (strpos($phone, '+') === false) {
            $phone = '+62' . $phone;
        }
        return $phone;
    }
    /**
     * generate perkiraan user id selanjutanya
     * 
     * @return int perkiraan user id selanjutnya
     */
    public function nextUserId()
    {
        $nextId = $this->model->select('id')->orderBy('id', 'DESC')->first()->toArray();
        $nextId = $nextId['id'] + 1;
        return $nextId;
    }
    /**
     * generate user_idcode
     * format YYYYMMDD[USER_ID][KARAKTER 0 HINGGA 8 DIGIT][NO URUT PENDAFTARAN HARI INI]
     * 
     * @return int perkiraan user_idcode
     */
    public function generateUserIdcode()
    {
        $count = $this->model->whereDate('created_at', '=', Carbon::today()->toDateString())->count() + 1;
        $nextId = $this->nextUserId();

        $zero = str_repeat('0', 8 - strlen($count . $nextId));

        $idcode = Carbon::today()->format('Ymd') . $nextId . $zero . $count;
        return $idcode;
    }

    
    
    /**
     * Manage USER ROLE
     * -------------------------------------------------------------------------
     */
        
    /**
     * 
     * @param type $userId
     * @return boolean|array list role user, format mirip data role di APPSSession
     */
    public function getUserRole($userId,$withoutTime=true)
    {
        $response = [];
        $userRoleData = UserRole::where('user_id',$userId)->get();
        if(!$userRoleData)return false;
        foreach ($userRoleData as $key => $value) {
            $roleData = Role::where('id',$value->role_id)->first();
            if($roleData){
                $roleData = $roleData->toArray();
                $roleData['is_main_role'] = $value->is_main_role;
                $roleData['has_auth_grant'] = $value->has_auth_grant;            

                if($withoutTime){
                    unset($roleData['created_at'],$roleData['updated_at']);
                }
                $response[$roleData['role_code']] = $roleData;
            }
        }
        
        return $response;
    }
    /**
     * 
     * @param type $userId
     */
    public function generateUserRole($userId)
    {
        $dataRole = UserRole::with(['role'])->where('user_id', $userId)->get()->toArray();
        if(!$dataRole) return '';
        foreach ($dataRole as $value) {
            $data[] = $value['role']['role_code'];
        }        
        return ';'.implode(';', $data).';';
    }
    
    /**
     * untuk tambah 1 role baru ke user
     */
    public function addUserRole($userId,$roleCode,$isMainRole=0,$hasAuthGrant=0)
    {
        $role = $this->_getOne(new Role,['role_code',$roleCode]);
        if(!$role)return false;
                
        //cek pastikan user role belum terdaftar
        $userRole = $this->_getOne(new UserRole,[['user_id',$userId],['role_id',$role['id']]]);
        if($userRole)return false;
        $this->_create(new UserRole, [
            'user_id' => $userId,
            'role_id' => $role['id'],
            'is_main_role' =>$isMainRole,
            'has_auth_grant'=>$hasAuthGrant,
        ]);
                        
        //update role di table user
        $this->updateUser($userId, [
            'role'=> $this->generateUserRole($userId)
            ]);
        return $role;
    }

    /**
     * untuk update role user
     * 
     * @param integer $userId
     * @param array|string $newRoleCode
     */
    public function updateUserRole($userId,$newRoleCode,$hasAuthGrant=0){
        
        if(!is_array($newRoleCode))$newRoleCode = [$newRoleCode];

        $roleIds = Role::whereIn('role_code', $newRoleCode)->orderBy('level','ASC')->get()->pluck('id');
        
        if (count($roleIds)<=0) {
            $this->error = 'Role not defined.';
            return false;
        }

        //delete semua role yang tidak terpilih
        UserRole::where('user_id',$userId)->whereNotIn('role_id', $roleIds)->delete();

        $roles = Role::whereIn('role_code', $newRoleCode)->orderBy('level','ASC')->get();        
        $first = true;
        $isMainRole = 1;
        foreach ($roles as $role) {

            if(UserRole::where('user_id',$userId)->where('role_id',$role->id)->exists()){
                $this->_update(new UserRole,[['user_id',$userId],['role_id',$role->id]], [
                    'has_auth_grant'=>$hasAuthGrant,
                    'is_main_role'=>$isMainRole,
                ]);
            }else{
                $this->_create(new UserRole, [
                    'user_id'=>$userId,
                    'role_id'=>$role->id,
                    'has_auth_grant'=>$hasAuthGrant,
                    'is_main_role'=>$isMainRole,
                ]);
            } 
            
            if($first){
                $first = false;
                $isMainRole = 0;
            }     
        }
                           
        //update role di table user
        $this->updateUser($userId, [
            'role'=> $this->generateUserRole($userId),
            'level'=>$role->level
        ]);
    }

    /**
     * belum dipakai
     */
    // public function updateUserRole($key,$data)
    // {
    //     //cek pastikan user role sudah terdaftar
    //     $userRole = $this->_getOne(new UserRole, $key);
    //     if(!$userRole)return false;
    //     if(!isset($data['role_code'])){
    //         $data['role_code'] = $userRole['role_code'];
    //     }
           
    //     $roleData = $this->_update(new UserRole,['user_id'=>$userRole['user_id'],'role_code'=>$userRole['role_code']], [
    //         'role_code' => $data['role_code'],
    //         'is_main_role' =>isset($data['is_main_role'])&&$data['is_main_role']?1:0,
    //         'has_auth_grant'=>isset($data['has_auth_grant'])&&$data['has_auth_grant']?1:0,
    //     ]);       
                        
    //     //update role di table user
    //     $this->updateUser($userRole['user_id'], ['role'=> $this->generateUserRole($userRole['user_id'])]);
    //     return $roleData;
    // }
    
    public function deleteUserRole($userId,$roleCode)
    {
        $role = $this->getOne(['role_code',$roleCode]);
        if(!$role)return false;
        
        //delete role dari user role
        $roleData = $this->_delete(new UserRole,[
            'user_id' => $userId,
            'role_code' => $role['role_code']
        ]);
        
        //delete role di table user
        $this->updateUser($userId, ['role'=> $this->generateUserRole($userId)]);
        
        return $roleData;
    }        
    /**
     * Belum selesai
     */
    public function setAuthGrant($userId,$roleCode)
    {
        $user = User::find($userId);
        $user->roles()->where('has_auth_grant');
        return User::find($userId)->update(['status' => 2]);
    }

    /**
     * tidak jadi digunakan
     */
    // public function updateRole($userId, $userData = null)
    // {
        
        // if (isset($userData['role']) && is_array($userData['role'])) {
        //     $userRole = UserRole::where('user_id', $userId)->pluck('role_code')->toArray();

        //     $hasIsMainRole = 0;
        //     foreach ($userData['role'] as $value) {
        //         //simpan semua nama role yg dipilih untuk keperluan delete role yg tidak dipilih
        //         $role[] = $value['role_code'];
        //         if (isset($value['role_code']) && $value['role_code']) {
        //             //tandai apakah ada is_main_role yg dipilih, jika tidak ada maka
        //             //nanti di proses selanjutnya tambahkan is_main_role ke member
        //             if (isset($value['is_main_role']) && $value['is_main_role']) {
        //                 $hasIsMainRole++;
        //             }
        //             //inputkan role baru yang dipipilih
        //             if (!in_array($value['role_code'], $userRole)) {
        //                 $this->addUserRole(
        //                     $userId,
        //                     $value['role_code'],
        //                     isset($value['is_main_role']) && $value['is_main_role'] ? 1 : 0,
        //                     isset($value['has_auth_grant']) && $value['has_auth_grant'] ? 1 : 0,
        //                     false
        //                 );
        //                 //update role yang memang sebelumnya telah ada
        //             } else {

        //                 $this->updateUserRole([
        //                     'user_id' => $userId,
        //                     'role_code' => $value['role_code']
        //                 ], [
        //                     'is_main_role' => isset($value['is_main_role']) && $value['is_main_role'] ? 1 : 0,
        //                     'has_auth_grant' => isset($value['has_auth_grant']) && $value['has_auth_grant'] ? 1 : 0
        //                 ]);
        //             }
        //         }
        //     }

        //     foreach ($userRole as $value) {
        //         //hapus role lama yang tidak dipilih
        //         if (!in_array($value, $role)) {
        //             $this->deleteUserRole($userId, $value);
        //         }
        //     }

        //     //jika tidak memilih main role atau yg dipilih lebih dari 1 maka
        //     //set members sebagai main role
        //     if (!$hasIsMainRole || $hasIsMainRole > 1) {

        //         $this->updateUserRole([
        //             'user_id' => $userId,
        //             'role_code' => 'member'
        //         ], ['is_main_role' => 1]);
        //     }
        //     unset($userData['role']);
        // }

        //        if(isset($userData['is_main_role']) && is_array($userData['has_auth_grant'])){
        //            foreach ($userData['role_code'] as $key => $value) {
        //                if(!in_array($value, $userData['has_auth_grant'])){
        //                    UserRole::where(['user_id' => $userId,'role_code' => $value])->update(['has_auth_grant'=>0]);
        //                }
        //            }
        //
        //            foreach ($userData['has_auth_grant'] as $item) {
        //                if(in_array($item, $userData['role_code'])){
        //                    UserRole::where(['user_id' => $userId,'role_code' => $item])->update(['has_auth_grant'=>1]);
        //                }
        //            }
        //
        //            UserRole::where('user_id', $userId)->update(['is_main_role'=>0]);
        //            UserRole::where(['user_id' => $userId,'role_code' => $userData['is_main_role']])->update(['is_main_role'=>1]);
        //        }

    // }
}
