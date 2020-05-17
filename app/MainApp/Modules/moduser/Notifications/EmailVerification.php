<?php

namespace App\MainApp\Modules\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $userId,$isSecondary;

    /**
     * Create a new notification instance.
     *
     * @param type $userId
     * @param type $isSecondary
     */
    public function __construct($userId,$isSecondary=false)
    {
        $this->userId = $userId;
        $this->isSecondary = $isSecondary;
        
        // $this->connection = config('bssystem.queue_connection_ac');
        $this->queue = 'verification';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return ['mail', DbChannels::class];
        return ['mail'];
        // return [DbChannels::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = new \App\MainApp\Modules\moduser\Mail\EmailVerification($this->userId,$this->isSecondary);
        return $email->to($notifiable->email);        
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
