<?php

namespace App\MainApp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

// use GuzzleHttp\Client;

use App\Base\RepoCacheTrait;
// use Facades\App\Services\Excel;

use Facades\App\MainApp\Repositories\Absensi;

use Illuminate\Support\Facades\Log;

/**
 * prosess jobs dari queue yg digenerate jobs ProcessUserUpdate
 */
class InsertFromFileRaw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RepoCacheTrait;

    protected $absensiRawFileId;
    public $tries = 5;
    public $retryAfter = 10;
    public $timeout = 120000;
    
    /**
     * Create a new job instance.
     *
     * @param string $fileUpload path file upload nya
     * @return void
     */
    public function __construct($absensiRawFileId)
    {
        $this->absensiRawFileId = $absensiRawFileId;
        
        // $this->connection = config('bssystem.queue_connection_ac');
        $this->queue = 'default';
    }

    
    public function failed(Exception $exception)
    {        
        Log::error("InsertFromFileRaw Error : \n".$exception->getTraceAsString());
        report($exception); //lanjutkan error ke login (meureun)
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Absensi::InsertFromFileRaw($this->absensiRawFileId);
    }

}
