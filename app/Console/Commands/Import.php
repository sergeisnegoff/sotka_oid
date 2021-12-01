<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportController;
use App\Models\importProducts;
use Illuminate\Console\Command;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $start = microtime(true);
        $task = $this->argument('task');
        switch ($task) {
            case 'import_products':
                (new ImportController())->products();

                $finish = microtime(true);
                $delta = $finish - $start;

                importProducts::create(['time' => $delta, 'table' => 'import_products']);
                break;
            case 'import_contr':
                (new ImportController())->contragents();

                $finish = microtime(true);
                $delta = $finish - $start;

                importProducts::create(['time' => $delta, 'table' => 'import_contr']);
            break;
            case 'orders':
                (new ImportController())->orders();

                $finish = microtime(true);
                $delta = $finish - $start;

                importProducts::create(['time' => $delta, 'table' => 'import_orders']);
            break;
            case 'managers':
                (new ImportController())->managers();

                $finish = microtime(true);
                $delta = $finish - $start;

                importProducts::create(['time' => $delta, 'table' => 'managers']);
        }
        return 0;
    }
}
