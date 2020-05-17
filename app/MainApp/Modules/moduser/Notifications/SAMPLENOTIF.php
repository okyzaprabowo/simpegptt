<?php

namespace App\MainApp\Modules\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\MainApp\Modules\moduser\Channels\DbChannels;

class SAMPLENOTIF extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $someData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($someData)
    {
        $this->someData = $someData;
        // $this->connection = config('bssystem.queue_connection_ac');
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

        //create email langsung
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
        
        // //atau menggunakan class email
        // $email = new \App\MainApp\Mail\PengajuanCreated($this->pengajuan);
        // return $email->to($notifiable->email);
        // //untuk render email template jika diperlukan
        // //$email = (new MailInvoice($this->title,$this->invoice))->render();
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'subject' => $this->title,
            'description' => 'Proses Packing Sudah selesai. Pesanan sedang dalam Pengiriman',
            'link_web' => [//parameter wajib
                'link' => '',
                'route' => 'member.order.detail',
                'parameter' => ['invoiceId' => $this->someData]
            ],
            'link_app' => ''//parameter wajib
        ];
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
            'subject' => $this->title,
            'description' => 'Proses Packing Sudah selesai. Pesanan sedang dalam Pengiriman',
            'link_web' => [
                'link' => '',
                'route' => 'member.order.detail',
                'parameter' => ['invoiceId' => $this->invoice['invoice']]
            ]
        ];
    }
}
