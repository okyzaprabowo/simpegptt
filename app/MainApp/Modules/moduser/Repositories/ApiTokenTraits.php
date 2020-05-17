<?php

namespace App\MainApp\Modules\moduser\Repositories;

use App\MainApp\Modules\moduser\Models\ApiToken;

trait ApiTokenTraits
{    
    
    public function isTokenValid($tokenData)
    {        
        if(is_array($tokenData)){
            $lastUpdate = $tokenData['updated_at'];
        }else{
            $tokenData = $this->getToken($tokenData);
            if(!$tokenData){
                $this->error = 'Token Not Found.';
                return false;
            }
            $lastUpdate = $tokenData['updated_at'];
        }
        
        //jika sudah melebihi batas waktu
        if(now() > now()->addHours(config('bssystem.sso.session_lifetime'))){
            $this->deleteToken($tokenData['api_token']);
            return false;
        }
        
        return true;
    }
    
    
    public function listTokenBySessionId($sessionId)
    {
        $apiToken = ApiToken::where('session_id',$sessionId)->get();
        if(!$apiToken){
            $this->error = 'Token Not Found.';
            return false;
        }
        return $apiToken->toArray();
    }

    /**
     * 
     * @param type $dataId
     * @return type
     */
    public function getToken($token)
    {        
        $apiToken = ApiToken::where('api_token',$token)->first();
        if(!$apiToken){
            $this->error = 'Token Not Found.';
            return false;
        }
        return $apiToken->toArray();
    }
    
    /**
     * 
     * @param type $userId , user id atau apps id
     * @param type $ssoId
     * @param type $isMobileAppsToken
     * @param type $deviceId
     * @param mixed $pushDetail false jika tidak ada push, atau array jika ada push
     *      type
     *      token
     * @return type
     */
    public function generateToken($userId,$isMobileAppsToken=0,$deviceId='',$pushDetail=false)
    {
        $data['api_token'] = hash('sha256', 'token'.$userId.'.'.now());
        $data['session_id'] = session()->exists('_token')?session()->getId():'';
        $data['is_mobileapps_token'] = $isMobileAppsToken;
        
        if($isMobileAppsToken && $deviceId)$data['device_id'] = $deviceId;                
            
        if($pushDetail&& is_array($pushDetail)){
            if(isset($pushDetail['type']))$data['push_type'] = $pushDetail['type'];
            if(isset($pushDetail['token']))$data['push_token'] = $pushDetail['token'];
        }
        
        $data['user_id'] = $userId;
        
        $apiTokenData = ApiToken::create($data);
        return $apiTokenData->toArray();
    }
        
    /**
     * update updated_at by sso id
     * 
     * @param type $ssoId
     * @param type $dateTime
     * @return boolean
     */
    public function setSingleTokenLastUpdate($token,$dateTime=false)
    {
        if(!$dateTime)$dateTime = now()->toDateTimeString();
        ApiToken::where('api_token',$token)->update(['updated_at'=>$dateTime]);
        return true;
    }
    
    /** 
     * delete api token
     * 
     * @param strung $token token yang akan didelete
     */     
    public function deleteToken($token)
    {        
        ApiToken::where('api_token',$token)->delete();
        return true;
    }
    public function deleteTokenBySessionId($sessionId)
    {
        ApiToken::where('session_id',$sessionId)->delete();
        return true;
    }
    
}