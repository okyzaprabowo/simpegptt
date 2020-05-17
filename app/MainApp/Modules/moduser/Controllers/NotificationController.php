<?php

namespace App\MainApp\Modules\moduser\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Facades\App\MainApp\Modules\moduser\Services\UserNotifSrv;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

use App\Base\BaseController;

class NotificationController extends BaseController
{        
    public function __construct()
    {        
        $this->forceApiOutput();
    }
    
    public function setnotif() {
        dd(UserRepo::listUser(['profile'=>[['divisi_id',1]],['role','LIKE','%;superadmin;%']]));
        UserRepo::notify(
                        1, 
                        new \App\MainApp\Notifications\PengajuanCreated(['id'=>1,'nama_pekerjaan'=>'Pengadaan wc umum'])
                    );
        return $this->done();
    }
    /**
     * API, IFRAME CALL & WEB VIEW
     * 
     * List notifikasi
     * 
     * @param Request $request
     */
    public function index(Request $request)
    {
//        dd(parse_url('http://localhost/BS/APPS-Account/public/notification?limitStart=0'));
        $userId = UserAuth::user('id');
        
        $this->output['viewdata']['filter']['readStatus'] = $request->input('status',0);
        $this->output['viewdata']['filter']['type'] = $request->input('type',false);
        // $this->output['viewdata']['filter']['isResellerOnly'] = $request->input('isResellerOnly',null);
        // $this->output['viewdata']['filter']['isResellerOnly'] = strtoupper($this->output['viewdata']['filter']['isResellerOnly'])=='NULL'?null:$this->output['viewdata']['filter']['isResellerOnly'];
          
        $this->output['viewdata']['offset'] = $request->input('offset',0);
        $this->output['viewdata']['limit'] = $request->input('limit',10);
        
        $this->output['data']['notification'] = UserNotifSrv::listNotif(
            $userId,
            $this->output['viewdata']['filter'],
            $this->output['viewdata']['offset'],
            $this->output['viewdata']['limit']
        );
        $this->output['data']['summary'] = UserNotifSrv::getSummary($userId);
                
        //jika menyertakan set read
        if($request->input('setRead')){
            UserNotifSrv::setRead($userId,UserNotifSrv::getLastNotifId());
        }
        
        return $this->done();
    }
    
    /**
     * API, IFRAME CALL & WEB VIEW
     * detail notifikasi
     * 
     * @param Request $request
     * @param type $notificationId
     * @return type
     */
    public function detail(Request $request,$notificationId=false)
    {
        
//        $userData = Auth::user()->toArray();
        $userId = UserAuth::user('id');//isset($userData['user_id'])?$userData['user_id']:$userData['id'];
        
        $this->output['viewdata']['onIframe'] = false;
        //jika iframe
        if(true){//$this->hasReferer()){//&& $request->input('iframeInclude') ){
            $this->response = 'account.notification_detail';
            $this->output['viewdata']['onIframe'] = true;
        }else{
            $this->response = redirect()->route('home');
        }
        
        $this->output['listdata'] = UserNotifSrv::getNotif($userId,$notificationId);
        
        if(!$this->output['listdata']){
            $this->output['message'] = 'Notification Not Found';
            $this->response = redirect()->route('account.notification');
        }
        
        //jika menyertakan set read
        if($request->input('setRead')){
            UserNotifSrv::setRead($userId,$notificationId);
        }
        
        return $this->done();
    }
    
    /**
     * API, IFRAME CALL & WEB VIEW
     * 
     * @param Request $request
     * @param type $notificationId
     * @return type
     */
    public function deleteNotif(Request $request,$notificationId=false)
    {                        
//        $userData = Auth::user()->toArray();
        $userId = UserAuth::user('id');//isset($userData['user_id'])?$userData['user_id']:$userData['id'];
        
        $this->response = redirect()->route('account.notification');
        
        if(UserNotifSrv::deleteNotif($userId,$notificationId)){
            $this->output['data'] = true;
            $this->output['message'] = 'Notification deleted';
            $this->output['message_type'] = 'success';
        }else{
            $this->output['status'] = 400;
            $this->output['data'] = false;
            $this->output['message'] = 'Notification not found';
            $this->output['message_type'] = 'warning';
        }
        return $this->done();
    }
    
    /**
     * API OUTPUT
     * 
     * list tipe notifikasi
     * 
     * @param Request $request
     */
    public function listType(Request $request)
    {
        $this->output['data'] = UserNotifSrv::listType();
        
        
        return $this->done();
    }
    
    public function setRead(Request $request,$notificationId=false)
    {
        if($notificationId==false){
            $notificationId = $request->input('notificationId');
        }
        return $this->_setRead($request,$notificationId,true);
    }
    
    public function setUnread(Request $request,$notificationId=false)
    {
        if($notificationId==false){
            $notificationId = $request->input('notificationId');
        }
        return $this->_setRead($request,$notificationId,false);
    }
    
    private function _setRead($request,$notificationId,$setRead)
    {
        $userData = Auth::user()->toArray();
        $userId = isset($userData['user_id'])&&$userData['user_id']?$userData['user_id']:$userData['id'];
        
        $this->response = redirect()->route('account.notification');
        
        $notificationIds = $notificationId?$notificationId:$request->input('notificationId');
                
        if(UserNotifSrv::setRead($userId,$notificationIds,$setRead)){
            $this->output['data'] = true;
            $this->output['message'] = __('ac_account_notif.'.($setRead?'notice_notifreadsuccess':'notice_notifunreadsuccess'));
            $this->output['message_type'] = 'success';
        }else{
            $this->output['status'] = 400;
            $this->output['data'] = false;
            $this->output['message'] = __('ac_account_notif.notice_notifnotfound');
            $this->output['message_type'] = 'warning';
        }
        return $this->done();
    }
}