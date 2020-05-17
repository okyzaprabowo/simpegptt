<?php

namespace App\MainApp\Modules\moduser\Services;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Repositories\RoleRepo;
use Illuminate\Support\Facades\Auth;

/**
 * Library untuk akses SSO service
 * 
 * SSOSession structure :
 *      
 */
class UserAuth
{    
    protected $isLogin=false;
    protected $isApiCall=false;
    protected $token;
    protected $userData;
    protected $userRoleList;
    protected $userRole;
    protected $userRoleCode;
    
    public function setInit($isApiCall=false,$userData=false,$userRole=false)
    {
        $this->isApiCall = $isApiCall;
        if($isApiCall){
            if(Auth::check()){
                $userId = Auth::user()->user_id;
                if($userId){
                    $this->userData = UserRepo::getUser($userId);
                    $listUserRole = UserRepo::getUserRole($userId);
                    
                    foreach($listUserRole as $key => $val) {
                        if($val['is_main_role']){
                            $this->userRoleCode = $key;
                            $this->userRole = $val;
                            break;
                        }
                    }
                    // $this->userRole = array_pop($listUserRole);
                }
            }
        }else{
            $this->userData = $this->getUserSessionData();
            $this->userRole = $this->getUserSessionRole();
            $this->userRoleCode = $this->getActiveUserRoleCode();
        }
    }
    
    /**
     * generate current timestamps
     * 
     * @return type
     */
    public function getCurTimeStamp()
    {
        $now = now();
        $data['lastUpdate'] = $now->toDateTimeString();
        $data['validUntil'] = $now->addHour(config('session.lifetime'))->toDateTimeString();
        return $data;
    }
    
    public function getToken($field=false)
    {
        if($field && session('APPSSession.token.'.$field)){
            $data = session('APPSSession.token.'.$field);
        }else{
            $data = session('APPSSession.token');
        }
        return $data;
    }

    public function getUserSessionData($field=false)
    {
        if($field && session('APPSSession.user.'.$field)){
            $data = session('APPSSession.user.'.$field);
        }else{
            $data = session('APPSSession.user');
        }
        return $data;
    }
    
    public function getUserSessionRole($field=false)
    {
        if($field && session('APPSSession.role.'.$field)){
            $data = session('APPSSession.role.'.$field);
        }else{
            $data = session('APPSSession.role');
        }
        return $data;
    }

    public function getActiveUserRoleCode()
    {
        return session('APPSSession.role_code');
    }
    
    public function getSessionLastUpdate()
    {
        return session('APPSSession.lastUpdate');
    }

    public function getSessionValidUntil()
    {
        return session('APPSSession.validUntil');
    }

    /**
     * set session saat login
     * 
     * @param type $userId
     */
    public function setUser($userId,$apiTokenData=false)
    {
        if(!$apiTokenData){
            $apiTokenData = UserRepo::generateToken($userId);
        }
        
        $sessionData = $this->getCurTimeStamp();
        $userData = UserRepo::getUser($userId);
        $role = UserRepo::getUserRole($userData['id']);

        foreach($role as $key => $val) {
            $roleCode = $key;
            if($val['is_main_role']){
                $roleCode = $key;
                break;
            }
        }

        $this->setSession([
            'token' => $apiTokenData,            
            'user' => $userData,
            'role' => $role,
            'role_code' => $roleCode,
            'lastUpdate' => $sessionData['lastUpdate'],
            'validUntil' => $sessionData['validUntil']
        ]);
    }

    /**
     * ganti role user yang sedang login
     */
    public function setActiveRole($roleCode)
    {
        if($this->role($roleCode)){
            $this->setSession([
                'role_code' => $roleCode
            ]);
            return true;
        }
        return false;
    }

    public function updateSessionId()
    {
        $this->setSession(['sessionId'=>session()->getId()]);
    }
    /**
     * unset session saat logout
     * 
     * @param type $userId
     */
    public function unsetUser()
    {
        $sessionData = $this->getCurTimeStamp();        
        $this->setSession([
                'token' => '',
                'lastUpdate' => $sessionData['lastUpdate'],
                'validUntil' => $sessionData['validUntil'],
                'user' => '',
                'role' => '',
                'role_code' => ''
            ]);
    }
    
    /**
     * set session saat pertama kali dapet dari account center
     * @param array $apiData variable session SSO yg akan diubah
     */
    public function setSession(Array $data)
    {
        //$availableKey = ['id', 'lastUpdate','validUntil','appsToken','userToken','userData'];
        foreach ($data as $key => $value) {
            session()->put('APPSSession.'.$key,$value);
        }        
        session()->save();
    }

    /**
     * =============================================================================================
     */
    public function isLogin()
    {
        return Auth::check() && is_array($this->userData);
    }
    
    /**
     * GET data user dari Account Center
     * @param type $field
     * @return type
     */
    public function user($field=false,$default=false)
    {
        if($field==false)return $this->userData;
        return isset($this->userData[$field])?$this->userData[$field]:$default;
    }
    
    /**
     * GET data roles (list roles user yang login) dari Account Center
     * @param type $field
     * @return type
     */
    public function role($field=false,$default=false)
    {
        if($field==false)return $this->userRole;
        return isset($this->userRole[$field])?$this->userRole[$field]:$default;
    }
    
    
    public function isPhoneVerified()
    {        
        if(isset($this->userData['phone_verified_at']) && $this->userData['phone_verified_at']){
            return true;
        }
        return false;
    }
    
    public function isEmailVerified()
    {        
        if(isset($this->userData['email_verified_at']) && $this->userData['email_verified_at']){
            return true;
        }
        return false;
    }
        
    /**
     * cek status user apakah posisi tidak aktif / banned
     * 
     * @return boolean
     */
    public function isBanned()
    {
        if($this->localUser['status'] == 0){
            return true;
        }
        return false;
    }

    /**
     * ROLE CHECK
     * -------------------------------------------------------------------------
     */
    
    /**
     * cek apakah user yang online memiliki akses role "rolde_code"
     * 
     * @param type $roleCode
     * @return boolean
     */
    public function hasRole($roleCode)
    {
        if(isset($this->userData['role']) && strpos($this->userData['role'],';'.$roleCode.';')!==false){
            return true;
        }
        return false;
    }
    
    /**
     * cek apakah user yang online adalah sebagai "rolde_code"
     * 
     * @param type $roleCode
     * @return boolean
     */
    public function is($roleCode)
    {
        if(!empty($this->userRoleCode) && $this->userRoleCode == $roleCode){
            return true;
        }        
        return false;
    }
    
    public function hasAccess($key, $subKey='has_access')
    {
        if(isset($this->userRole[$this->userRoleCode])){
            if(
                isset($this->userRole[$this->userRoleCode]['rule'][$key][$subKey]) 
                && $this->userRole[$this->userRoleCode]['rule'][$key][$subKey] == 0
            ){
                return false;
            }
        }
        return true;
    }
    /**
     * cek apakah user yang login memiliki grant access ke role_code
     * 
     * @param string $roleCode role_code yang dicek nya
     */
    public function isGranted($roleCode=false)
    {
        $data = UserRepo::getUserRole($this->userData['id']);
        
        if(!is_array($roleCode))$roleCode = [$roleCode];
        foreach ($data as $role)
        {
            if(in_array($role['role_code'], $roleCode) && $role['has_auth_grant'] == 1)
                return true;
        }
        return false;
    }
}