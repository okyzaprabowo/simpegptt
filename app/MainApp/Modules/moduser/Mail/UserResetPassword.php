<?php

namespace App\MainApp\Modules\moduser\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
// use App\Mailling;

use Facades\App\MainApp\Modules\moduser\Repositories\UserRepo;

class UserResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    protected $userId;
    
    /**
     * Create a new message instance.
     *
     * @param array $user array data user
     *      name
     *      email
     *      resetPasswordUrl
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userData = UserRepo::resetPasswordEmailDataFormat($this->userId);
        return $this->subject(__('email.resetpassword_subject'))->view('user.emails.userResetPassword')->with($userData);
    }
}
