<?php
namespace App\MainApp\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

//...use semua class dependency disini
//use App\Jobs\DueNotification;
use App\MainApp\Jobs\SetAbsensiAlpa;

use App\MainApp\Jobs\GenerateAbsensiDefault;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //register scheduller
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
			
			//....rigister scheduller disini
            // $schedule->job(new SetAbsensiAlpa)->dailyAt('13:00');
            //jalankan auto generate absensi tiap awal bulan
            $schedule->job(new GenerateAbsensiDefault)->monthlyOn(1,'00:01')->withoutOverlapping();
            $schedule->job(new SetAbsensiAlpa)->dailyAt('00:01')->withoutOverlapping();
        });
        
        //...register middleware disini
        // $this->app['router']->pushMiddlewareToGroup('api', \App\MainApp\Middleware\InstansiCheck::class);

        DB::listen(function ($query) {
            // $query->sql
            // $query->bindings
            // $query->time
            if(stripos($query->sql,'absensi')!=false && stripos($query->sql,'select')===false){
                $sql = str_replace('?', "'?'", $query->sql);
                $sql = vsprintf(str_replace('?', '%s', $sql), $query->bindings);                
                Log::info('[QUERY] : '.$sql);
            }
        });
    }

    public function register()
    {
        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('UserAuth', UserAuth::class);
        });
        
    }
}