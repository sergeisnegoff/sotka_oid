<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

class OrdersTotalAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = Order::all();
        foreach ($orders as $order){
            $order->amount = $order->total();
            $order->save();
        }
        $users = User::all();
        foreach ($users as $user){
            $sum = 0;
            foreach ($user->orders as $order) {
                $sum += $order->amount;
            }
            $user->orders_total_amount = $sum;
            $user->save();
        }

        return 0;
    }
}
