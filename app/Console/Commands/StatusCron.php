<?php
namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class StatusCron extends Command
{
    protected $signature = 'status:cron';
    protected $description = 'Command description';

    public function handle()
    {
        $touched = Order::query()
            ->whereNotNull('updated_status')
            ->where('updated_status', '<', now()->subDays(7))
            ->update(['status' => 'Shipped']);

        info("Touched orders: $touched");
    }

}
