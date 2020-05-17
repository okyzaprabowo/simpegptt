<?php

namespace App\MainApp\Modules\moduser\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;

class UserActivation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    protected $userId,$isSecondary;
    
    
    /**
     * Create a new message instance.
     * 
     * @param type $userId
     * @param type $isSecondary
     */
    public function __construct($userId,$isSecondary=false)
    {
        $this->userId = $userId;
        $this->isSecondary = $isSecondary;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userData = UserRepo::verificationEmailDataFormat($this->userId,$this->isSecondary);
        return $this->subject(__('email.useractivation_subject'))->view('user.emails.userActivation')->with($userData);
    }
}
