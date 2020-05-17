<?php

namespace App\MainApp\Modules\moduser\Repositories;

//use Facades\App\MainApp\Modules\moduser\Repositories\AppsRepo;
//use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;
//use Carbon\Carbon;

//use semua model yg diperlukan
use App\MainApp\Modules\moduser\Models\SystemUserLog;
use App\MainApp\Modules\moduser\Models\AuthLog;

use App\Base\BaseRepository;

class UserLogRepo extends BaseRepository
{
    
    public function addLog($user_id,$section,$subsection=false,$log=false)
    {
        $logData['user_id'] = $user_id;
        $logData['section'] = $section;
        $logData['subsection'] = $subsection;
        $logData['content'] = is_array($log)?json_encode($log):$log;
        
        SystemUserLog::create($logData);
    }
    
    /**
     * Add Authorized log
     * 
     * @param int $user_auth_id user id admin yg memberikan autorisasi (supervisor)
     * @param int $user_id user id user yg meminta autorisasi
     * @param string $note catatan authorisasi nya, misal saat perubahan stock (stock OP name)
     * @param array $data erisi data yang berhubungan dengan perubahan data yang diberikan authorisasi nya
     * @param string $ruleGroup grup rule akses autoritinya (table rules di account center)
     * @param string $ruleKey key rule akses autoritinya (table rules di account center)
     */
    public function addAuthLog($user_auth_id,$user_id,$note='',$data='',$rule=false)
    {
        $ruleItem = ['',''];
        if($rule)$ruleItem = explode('.', $rule);
        
        $logData['auth_by'] = $user_auth_id;
        $logData['auth_note'] = $note;
        $logData['user_id'] = $user_id;
        $logData['rule_group'] = $ruleItem[0];
        $logData['rule_key'] = $ruleItem[1];
        $logData['data'] = is_array($data)?json_encode($data):$data;
        
        AuthLog::create($logData);
    }
    
    //user interaktion
    public function addActivityLog($user_id,$section,$log='')
    {
        $this->addLog($user_id, 'activity',$section,$log);
    }
    public function addSystemLog($user_id,$section,$log='')
    {
        $this->addLog($user_id, 'system',$section,$log);
    }
}