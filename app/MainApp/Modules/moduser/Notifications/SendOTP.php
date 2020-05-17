<?php

namespace App\MainApp\Modules\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\MainApp\Modules\moduser\Channels\SmsChannels;

class SendOTP extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $otpcode,$isMainPhone;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($otpcode,$phone=false)
    {
        $this->otpcode = $otpcode;
        $this->phone = $phone;
        // $this->connection = config('bssystem.queue_connection_ac');
        $this->queue = 'high';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SmsChannels::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toSms($notifiable)
    {
        return [
            'message'=>__('notification.yourotpcode',['otpCode'=>$this->otpcode]),
            'phone'=> $this->phone
            ];
    }
}
