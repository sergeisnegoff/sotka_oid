<?php

namespace App\Console\Commands;

use App\Jobs\GetDataFromExcelJob;
use App\Models\PreorderTableSheet;
use Illuminate\Console\Command;

class GetDataFromExcelJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:getdata {id}';

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
        $preorderTableSheet = PreorderTableSheet::query()
            ->where('id', $this->argument('id'))
            ->first();

        GetDataFromExcelJob::dispatch($preorderTableSheet);

        return 0;
    }
}
