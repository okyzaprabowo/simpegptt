<?php

namespace App\MainApp\Modules\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\MainApp\Modules\moduser\Channels\DbChannels;
/**
 * broad cast message dari admin ke member
 */
class WarningReport extends Notification implements ShouldQueue
{
    use Queueable;
    protected $body,$title;
    /**
     * Create a new notification instance.
     *
     * @return void
     * 
     * Data :
     * Event terbaru
     * Link event
     * 
     */
    public function __construct($title,$body)
    {
        $this->title = $title;        
        $this->body = $this->extractBody($body);
        // $this->connection = config('bssystem.queue_connection_ac');
    }
    
    public function extractBody($body){
        if(is_array($body)){
            $result = '<ul>';
            foreach ($body as $key => $value) {
                if(is_array($value))$value = $this->extractBody($value);
                $result .= '<li><b>'.$key.' :</b> '.$value.'</li>';
            }
            $result .= '</ul>';
            $body = $result;
        }
        return $body;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail',DbChannels::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = new \App\MainApp\Modules\moduser\Mail\WarningReport($this->title, $this->body);
        return $email->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function toDatabase($notifiable)
    {
//        $email = (new MailInvoice($this->title,$this->invoice))->render();
        return [
//            'is_reseller' => 1,
            'subject' => $this->title,
            'body' => $this->body,
//            'link_web' => '',
//            'link_web' => ''
//            'link_web' => route('member.order.detail',['invoiceId'=>$this->invoice['invoice']]),
//            'from' => [
//                'name' => 'Dari Admin'
//            ]
        ];
    }
    
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
