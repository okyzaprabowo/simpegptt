<?php

namespace App\MainApp\Modules\moduser\Auth\Controllers;

use Facades\App\MainApp\Modules\moduser\Repositories\UserNotifRepo;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Repositories\RoleRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Base\BaseController;

class TokenApiController extends BaseController
{
    
    /**
     * API OUTPUT ONLY
     * /auth/token/validate
     * 
     * validate token and get user data
     * 
     * @param Request $request
     */
    public function validateToken(Request $request)
    {
        $apiToken = $request->user()->toArray();        
        $isTokenValid = UserRepo::isTokenValid($apiToken['api_token']);
                                
        $response['status'] = 200;
        //jika token sudah tidak valid
        if(!$isTokenValid){
            $response['message'] = 'Token Invalid';
            $response['data'] = null;
            $response['errors'] = [true];
        }else{
            $response['message'] = 'Token Valid';
            $response['data'] = UserAuth::getCurTimeStamp();
            
            $response['data']['user'] = UserRepo::getUser($apiToken['user_id']);
            $response['data']['role'] = UserRepo::getUserRole($apiToken['user_id']);
            $notifToken = $request->input('pushNotifToken',false);
            //jika menyertakan update token notif
            if($notifToken){
                //jika token berubah maka subscribe ulang
                if($apiToken['push_token'] = $notifToken){
                    $notifChannel[] = 'all';
                    // if(isset($response['data']['role'][9]))$notifChannel[] = 'member';         
                    
                    UserNotifRepo::subscribeToChannel($notifChannel,$notifToken);
                    UserNotifRepo::unsubscribeFromChannel($apiToken['push_token'],$notifToken);
                    
                    UserNotifRepo::changePushToken($apiToken['push_token'],$notifToken);
                }
            }
            
            $response['errors'] = null;
            UserRepo::setSingleTokenLastUpdate($apiToken['api_token'],$response['data']['lastUpdate']);
        }
        
        return response()->json($response, 200);
    }
}