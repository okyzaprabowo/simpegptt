<?php

namespace App\MainApp\Modules\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserResetPassword extends Notification implements ShouldQueue
{
    use Queueable;
    protected $userId;

    /**
     * Create a new notification instance.
     *
     * @return void
     * 
     * Data :
     * Link Reset Password
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
        
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = new \App\MainApp\Modules\moduser\Mail\UserResetPassword($this->userId);
        return $email->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
