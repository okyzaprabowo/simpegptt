<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        
        $schedule->command('queue:work --daemon --tries=3 --queue=high')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --daemon --tries=3 --queue=verification,email')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --daemon --tries=3 --queue=default,low')->everyMinute()->withoutOverlapping();
        
        if(config('AppConfig')){

        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(app_path('MainApp/Console/Commands'));
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
