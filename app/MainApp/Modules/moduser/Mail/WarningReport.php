<?php

namespace App\MainApp\Modules\moduser\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WarningReport extends Mailable
{
    use SerializesModels;

    protected $body,$title;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title,$body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)->view('emails.warningReport')->with([
            'title' => $this->title,
            'body' => $this->body
        ]);
//        ->attach(
//            '/path/to/file', [
//                'as' => 'name.pdf',
//                'mime' => 'application/pdf',
//        ]);
    }
}