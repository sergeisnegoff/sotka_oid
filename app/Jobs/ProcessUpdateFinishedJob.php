<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessUpdateFinishedJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uuid;

    public function __construct(string $uuid) {
        $this->uuid = $uuid;
    }


    public function handle() {
        Log::channel('import')->info('finish', ['uuid' => $this->uuid]);
    }

}
