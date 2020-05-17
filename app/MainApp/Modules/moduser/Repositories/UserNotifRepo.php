<?php

namespace App\MainApp\Modules\moduser\Repositories;

use App\MainApp\Modules\moduser\Repositories\UserMessageTraits;
//use semua model yg diperlukan
use App\MainApp\Modules\moduser\Models\User;
use App\MainApp\Modules\moduser\Models\NotificationChannel;
use App\MainApp\Modules\moduser\Models\ApiToken;

use App\MainApp\Modules\moduser\Services\FirebaseApi;

use Validator;

use App\Base\BaseRepository;

class UserNotifRepo extends BaseRepository
{
    use UserMessageTraits;
    
    protected $available_channer;
    
    public function addMessage()
    {
        NotificationMessage;
    }
    
    public function deleteMessage()
    {
        
    }
    
    /**
     * 
     * @param int $userId
     * @param int $status
     * @param array $filter
     *      type : string, tipe notif (nama class notif nya)
     *      readStatus :
     *      q : string, query search
     * @param type $offset
     * @param type $limit
     * @return type
     */
    public function listNotif($userId,$filter=false,$offset=0,$limit=10)
    {
        $user = User::find($userId);
        if(!$user)return [];
        
        $model = $user->notifications()->where('notifiable_id',$userId)
            ->orderBy('created_at','DESC');
        
        $filter['searchField'] = ['name', 'email', 'phone'];
        $filter['function'] = function($data) use ($filter) {
            if(isset($filter['type'])&&$filter['type']){
                $data = $data->where(
                    'type',
                    'LIKE',
                    '%\\Notifications\\'.$filter['type']
                    );
            }
            return $data;
        };
        if(isset($filter['type']))unset($filter['type']);
        if(isset($filter['readStatus']))unset($filter['readStatus']);
        $data = $this->_list(
            $model, $filter, $offset, $limit, false
        );

        return $data;
    }
    
    /**
     * Notif channel
     * =========================================================================
     */    
    public function subscribeToAll($pushNotifToken)
    {
        return $this->subscribeToChannel('all',$pushNotifToken);
    }
    public function subscribeToReseller($pushNotifToken)
    {
        return $this->subscribeToChannel('reseller',$pushNotifToken);
    }
    public function subscribeToMember($pushNotifToken)
    {
        return $this->subscribeToChannel('member',$pushNotifToken);
    }
    public function subscribeToAdmin($pushNotifToken)
    {
        return $this->subscribeToChannel('admin',$pushNotifToken);
    }
    public function subscribeToErrorlog($pushNotifToken)
    {
        return $this->subscribeToChannel('errorlog',$pushNotifToken);
    }
    
    public function subscribeToChannel($channelIds,$pushNotifToken)
    {
        if(!is_array($channelIds))$channelIds = [$channelIds];
        
        foreach ($channelIds as $channelId) {
            if(config('AppConfig.packageLocal.moduser.notification.use_jobs')){
                \App\MainApp\Modules\moduser\Jobs\PushSubscribe::dispatch($channelId,$pushNotifToken);
            }else{
                return $this->doSubscribeToChannel($channelId,$pushNotifToken);
            }
        }   
    }
    /**
     * 
     * @param string $channelId channel/topic nya
     * @param mixed $pushNotifToken
     */
    public function doSubscribeToChannel($channelId,$pushNotifToken)
    {
        $channel = config('AppConfig.packageLocal.moduser.notification.channels.'.$channelId);
        
        //jika channel tidak terdaftar
        if(!$channel){
            return false;  
        }
                
        $token = ApiToken::where('push_token',$pushNotifToken)->first();
        
        if($token){
            $token = $token->toArray();
        //jika token tidak terdaftar maka tolak
        }else{
            return false;
        }
        
        //jika sudah terdaftar maka cuekin
        if(NotificationChannel::where('push_token',$pushNotifToken)
            ->where('push_type',$token['push_type'])
            ->exists()){
            return true;
        }
        
        $notifchannel = NotificationChannel::create([
            'user_id' => $token['user_id'],
            'push_type' => $token['push_type'],
            'push_token' => $token['push_token'],
            'channel' => $channelId,
        ]);
        
        switch ($token['push_type']) {
            case 2://pusher
                //...
                //$channel['pusher']
                break;
            case 3://aws_sns
                //...
                //$channel['aws_sns']
                break;
            case 1://firebase                
            default:
                FirebaseApi::subscribeToTopic($channel['firebase'],$pushNotifToken);
                break;
        }
        return true;
        
    }
    /**
     * -------------------------------------------------------------------------
     */
     
    /**
     * 
     * @param type $pushNotifToken
     * @return type
     */
    public function unsubscribeFromAll($pushNotifToken)
    {
        return $this->unsubscribeFromChannel('all',$pushNotifToken);
    }
    public function unsubscribeFromReseller($pushNotifToken)
    {
        return $this->unsubscribeFromChannel('reseller',$pushNotifToken);
    }
    public function unsubscribeFromMember($pushNotifToken)
    {
        return $this->subscribeToChannel('member',$pushNotifToken);
    }
    public function unsubscribeFromAdmin($pushNotifToken)
    {
        return $this->subscribeToChannel('admin',$pushNotifToken);
    }
    public function unsubscribeFromErrorlog($pushNotifToken)
    {
        return $this->unsubscribeFromChannel('errorlog',$pushNotifToken);
    }
    
    public function unsubscribeFromChannel($channelId,$pushNotifToken)
    {        
        if(config('AppConfig.packageLocal.moduser.notification.use_jobs')){
            \App\MainApp\Modules\moduser\Jobs\PushSubscribe::dispatch($channelId,$pushNotifToken,false);
        }else{
            return $this->doUnsubscribeFromChannel($channelId,$pushNotifToken);            
        }
    }
    /**
     * 
     * @param string $channelId channel/topic nya
     * @param type $pushNotifToken
     */
    public function doUnsubscribeFromChannel($channelId,$pushNotifToken)
    {
        $channel = config('AppConfig.packageLocal.moduser.notification.channels.'.$channelId);
        
        //jika channel tidak terdaftar
        if(!$channel){
            return false;  
        }
                
        $token = ApiToken::where('push_token',$pushNotifToken)->first();
        
        $notifChannel = NotificationChannel::where('push_token',$pushNotifToken)
            ->where('pusth_type',$token['pusth_type']);
        //jika sudah tidak ada maka lewat
        if(!$notifChannel->exists()){
            return true;
        }
        $notifChannel->delete();
        
        switch ($token['push_type']) {
            case 2://pusher
                //...
                //$channel['pusher']
                break;
            case 3://aws_sns
                //...
                //$channel['aws_sns']
                break;
            case 1://firebase                
            default:
                FirebaseApi::unsubscribeToTopic($channel['firebase'],$pushNotifToken);
                break;
        }
        return true;
    }
    
    /*
     * 
     */
    public function changePushToken($oldToken,$newToken)
    {
        $token = ApiToken::where('push_token',$oldToken)->first();
        if($token){
            $token->update(['push_token'=>$newToken]);
        }
        return true;
    }
    
    
}