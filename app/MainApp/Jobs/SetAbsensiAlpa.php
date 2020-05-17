<?php

namespace App\MainApp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
// use GuzzleHttp\Client;

use App\Base\RepoCacheTrait;
// use Facades\App\Services\Excel;

use Facades\App\MainApp\Repositories\Absensi;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * prosess jobs dari queue yg digenerate jobs ProcessUserUpdate
 */
class SetAbsensiAlpa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RepoCacheTrait;
    public $tries = 5;
    public $retryAfter = 10;
    public $timeout = 120000;

    // protected $absensiRawUploadId;
    
    /**
     * Create a new job instance.
     * 
     * @param string $fileUpload path file upload nya
     * @return void
     */
    public function __construct()
    {
        // $this->absensiRawUploadId = $absensiRawUploadId;
        
        // $this->connection = config('bssystem.queue_connection_ac');
        $this->queue = 'default';
    }

    public function failed(Exception $exception)
    {        
        Log::error("SetAbsensiAlpa Error : \n".$exception->getTraceAsString());
        report($exception); //lanjutkan error ke login (meureun)
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Absensi::kalkulasiAlpaNTelat(); 
    }

}
