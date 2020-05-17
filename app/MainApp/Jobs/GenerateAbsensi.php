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
 * generate absensi berdasarkan shift
 */
class GenerateAbsensi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RepoCacheTrait;

    protected $filterPegawai,$shiftId,$startDate,$endDate;
    public $tries = 5;
    public $retryAfter = 10;
    public $timeout = 120000;
    

    /**
     * Create a new job instance.
     * 
     * @param string $fileUpload path file upload nya
     * @return void
     */
    public function __construct($filterPegawai=false,$shiftId=false,$startDate=false,$endDate=false)
    {
        $this->filterPegawai = $filterPegawai;
        $this->shiftId = $shiftId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        
        // $this->connection = config('bssystem.queue_connection_ac');
        $this->queue = 'default';
    }

    public function failed(Exception $exception)
    {        
        Log::error("GenerateAbsensi Error : \n".$exception->getTraceAsString());
        report($exception); //lanjutkan error ke login (meureun)
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Absensi::generateDefaultAbsensiAll($this->filterPegawai,$this->shiftId,$this->startDate,$this->endDate); 
    }

}
