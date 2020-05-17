<?php

namespace App\MainApp\Modules\moduser\Channels;

use Illuminate\Notifications\Notification;

//use App\MainApp\Modules\moduser\Service\FirebaseAPI;

class DbChannels
{

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);

        $link_web = '';
        $link_apps = '';
        
        if(isset($data['link_web'])){
            $link_web = $data['link_web'];
            // if ($link_web['link']) {
            //     $link_web['link'] = $link_web['link'];
            // } else if ($link_web['route'] && $link_web['parameter']) {
            //     $link_web['link'] = route($link_web['route'], $link_web['parameter']);
            // } else {
            //     $link_web['link'] = route('member.notification.detail', ['notificationId' => $notification->id]);
            // }
            unset($data['link_web']);
        }
        if(isset($data['link_apps'])){
            $link_apps = $data['link_apps'];
            unset($data['link_apps']);
        }
        
        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification->id,

            'link_web' => $link_web,
            'link_apps' => $link_apps,
            
            'type' => get_class($notification),
            'data' => $data,
            'read_at' => null,
        ]);
    }

}
