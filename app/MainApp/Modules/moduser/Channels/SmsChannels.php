<?php

namespace App\MainApp\Modules\moduser\Channels;

use Illuminate\Notifications\Notification;
use App\MainApp\Modules\moduser\Service\ZendzivaApi;

class SmsChannels
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
        $smsObj = $notification->toSms($notifiable);
        $phone = $smsObj['phone']?$smsObj['phone']:$notifiable->phone;
        $send = (new ZendzivaApi)->to($phone)->text($smsObj['message'])->send();
        return $send;
    }
}