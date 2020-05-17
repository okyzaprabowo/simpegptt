<?php

namespace App\MainApp\Modules\moduser\Services;

//use Yajra\DataTables\Facades\DataTables;
use App\MainApp\Modules\moduser\Models\Notification as MNotif;
use App\MainApp\Modules\moduser\Models\User;
use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Modules\moduser\Repositories\UserNotifRepo;
use App\MainApp\Modules\moduser\Repositories\UserMessageTraits;
// use App\MainApp\Modules\moduser\Models\NotificationMessage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class UserNotifSrv
{
    use UserMessageTraits;
    
    //daftar jenis notifkasi, nama class notifiable yang ber channel databases
    protected $notificationType = [
        'AdminMessage' => 'Pesan dari Billionaire',
        'Invoice' => 'Tagihan',
        'InvoicePaid' => 'Tagihan Lunas',
        'Promo' => 'Promo',
    ];

    public function listType()
    {
        return $this->notificationType;
    }
    public function getSummary($userId)
    {
        return [
            'count' => MNotif::where('notifiable_id',$userId)->count(),
            'unread_count' => MNotif::where('notifiable_id',$userId)->whereNull('read_at')->count()
        ];
    }    
    
    /**
     * daftar notifkasi semua status
     */
    public function listNotifAll($userId,$offset=0,$limit=10)
    {
        return $this->listNotif($userId,['status',0],$offset,$limit);
    }
    
    /**
     * daftar notifkasi yang belum di read
     */
    public function listNotifNew($userId,$offset=0,$limit=10)
    {
        return $this->listNotif($userId,['status',1],$offset,$limit);
    }
    
    /**
     * daftar notifkasi yang sudah di read
     */
    public function listNotifReaded($userId,$offset=0,$limit=10)
    {
        return $this->listNotif($userId,['status',2],$offset,$limit);
    }
    
    /**
     * 
     * @param int $userId
     * @param int $status
     * @param array $filter
     *      type : string, tipe notif (nama class notif nya)
     *      reselerNotifOnly : booleadn
     *      readStatus :
     *      q : string, query search
     * @param type $offset
     * @param type $limit
     * @return type
     */
    public function listNotif($userId,$filter=false,$offset=0,$limit=10)
    {
        $notif = UserNotifRepo::listNotif($userId,$filter,$offset,$limit);
        $collection = collect($notif['data']);
        $collection->transform(function($i) {
            unset($i['notifiable_type'],$i['notifiable_id'],$i['updated_at']);
            // $i['type'] = strtolower(str_replace('moduser\\Notifications\\', '', $i['type']));
            return $i;
        });
        $notif['data'] = $collection->toArray();
        $this->lastNotificationList = $collection->toArray();
        return $this->lastNotificationList;
    }
    
    /**
     * get pagination notifkasi dari listNotification() terakhir
     * 
     * @param type $path
     * @return pagination
     */
    public function getLastNotifPagination($path='')
    {
        return UserNotifRepo::getPagination($path);
    }
    
    /**
     * get list id notifkasi dari listNotification() terakhir
     * @return array list id notification
     */
    public function getLastNotifId()
    {
        $collection = collect($this->lastNotificationList);
        $collection->transform(function($i) {
            return $i['id'];
        });
        return $collection->toArray();
    }
    
    public function getNotif($userId,$notifId)
    {
        $user = User::find($userId);
        if(!$user)return false;
        $data = $user->notifications()->where('id',$notifId)->first(); 
        if($data){
            $data = $data->toArray();
            unset($data['notifiable_type'],$data['notifiable_id'],$data['updated_at']);
            // $data['type'] = strtolower(str_replace('moduser\\Notifications\\', '', $data['type']));
            
        }else{
            $data = false;
        }
        return $data;
    }
    
    public function deleteNotif($userId,$notifId)
    {
        $user = User::find($userId);
        if(!$user)return false;
        $data = $user->notifications()->where('id',$notifId); 
        if($data){
            // $notifMessage = NotificationMessage::where('notification_id',$notifId);
            // if($notifMessage) $notifMessage->delete();            
            $data->delete();
            return true;
        }
        return false;
    }
    /**
     * 
     * @param type $userId
     * @param type $notifId
     * @param type $setRead
     * @return boolean
     */
    public function setRead($userId,$notifId=false,$setRead=true)
    {
        $user = User::find($userId);
        if(is_array($notifId)){
            $notif = $user->notifications->whereIn('id',$notifId);
        }else{
            $notif = $user->notifications->where('id',$notifId);
        }
        
        if($notif->count()){
            if($setRead){
                $notif->markAsRead();
            }else{
                if(is_array($notifId)){
                    MNotif::whereIn('id',$notifId)->update(['read_at' => NULL]);
                }else{
                    MNotif::where('id',$notifId)->update(['read_at' => NULL]);
                }
            }
        }
        return true;
    }
}