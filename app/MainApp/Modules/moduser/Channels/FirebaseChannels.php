<?php

namespace App\MainApp\Modules\moduser\Channels;

use Illuminate\Notifications\Notification;

use App\MainApp\Modules\moduser\Services\FirebaseApi;

class FirebaseChannels
{
    /**
     * Send the given notification. 
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws missingRecipient
     */

    public function send($notifiable, Notification $notification)
    {
        $notifMessage = $notification->toFirebase($notifiable);
        
        if(isset($notifMessage['topic'])){
            FirebaseApi::sendMessageToTopic($notifMessage['topic'],$notifMessage);
        }else if(isset($notifMessage['token'])){
            FirebaseApi::sendMessageToToken($notifMessage['token'],$notifMessage);            
        }else{
            
        }
        
//        $token1 = 'dbQCIUiid1w:APA91bE3ZH3ZZlP_9hL9lw-pL3SDjRdQ7q8pXhq4f-K4zkxpqaLn-HZiCf8BOvqgrlYGHccwKYI658oslg4Rd0UY4kjr8hBf23-593Lxd8UyenEg3Ls-YKQ9z4fcz3CSzRwvxSs3zBTx';
//        $token2 = 'doQA4MMaaOw:APA91bGdgPbPFxwa3ZY3XEm1jDUEqAwTS0s4sh4m5GF6ptsI8Anp-YWSUrh4vcZUGosOgZrthcOqzbF_FilevIUVuHIbxow_KCsLadaGf2yP3z_vj90a6PQrXrZbSIjcYZl9ts5xB9QA';
//        FirebaseApi::subscribeToTopic(
//            'reseller',[$token1,$token2]
//            );
//        
        
        return true;
    }

}