<?php

namespace App\MainApp\Modules\moduser\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;

use Facades\App\MainApp\Modules\moduser\Repositories\UserNotifRepo;

/**
 * prosess jobs dari queue yg digenerate jobs ProcessUserUpdate
 */
class PushSubscribe implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $channelId,$pushNotifToken,$isSubscribe;
    
    /**
     * Create a new job instance.
     *
     * @param array $userData Data user yang diupdate
     * @return void
     */
    public function __construct($channelId,$pushNotifToken,$isSubscribe=true)
    {
        $this->channelId = $channelId;
        $this->pushNotifToken = $pushNotifToken;
        $this->isSubscribe = $isSubscribe;
        // $this->connection = config('bssystem.queue_connection_ac');
        $this->queue = 'high';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->isSubscribe){
            UserNotifRepo::doSubscribeToChannel($this->channelId,$this->pushNotifToken);
        }else{
            UserNotifRepo::doUnsubscribeToChannel($this->channelId,$this->pushNotifToken);
        }
        
    }
}
