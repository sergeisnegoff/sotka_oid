<?php

namespace App\Console\Commands;

use App\Imports\ProductUpdateDirectImport;
use App\Imports\ProductUpdateImport;
use Illuminate\Console\Command;

class TestImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:import';

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
        ProductUpdateDirectImport::make()->import(storage_path('app/1c/Price/obshii.xls'));

    }
}
