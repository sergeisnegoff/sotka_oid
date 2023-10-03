<?php

namespace App\Console;

use App\Models\cronSettings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{

    const CONNECTION_DB = 'database';

    const QUEUE_IMPORT = 'import';
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\StatusCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work database', [
            '--queue' => 'import',
            '--daemon',
            '--tries' => 3,
            '--stop-when-empty',
            '--max-jobs=100',
        ])
            ->everyMinute()
            ->runInBackground()
            ->description('queue_import');

        $schedule->command('status:cron')
            ->everyMinute();

        $schedule->command('export:orders 8')
            ->dailyAt(setting('admin.FIRST_EXPORT'));

        $schedule->command('export:orders 12')
            ->dailyAt(setting('admin.SECOND_EXPORT'));

        $schedule->command('export:orders 16')
            ->dailyAt(setting('admin.THIRD_EXPORT'));

        $schedule->command('queue:restart')->daily()->runInBackground();

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
