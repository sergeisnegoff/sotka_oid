<?php

namespace App\Console;

use App\Models\cronSettings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

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
        $schedule->command('queue:work database --queue=import')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:restart')->daily();
//        $crons = cronSettings::all();
//        if (!empty($crons))
//            foreach ($crons as $cron) {
//                $time = $cron->minute.' '.$cron->hour.' '.$cron->day.' '.$cron->month.' '.$cron->week_day;
//
//                $schedule->command('import '.$cron->table)->cron($time);
//            }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
