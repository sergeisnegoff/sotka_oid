<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCleanTotalJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ids;

    public function __construct(array $ids) {
        $this->ids = $ids;
    }

    public function handle() {
        Product::query()
            ->whereNotIn('oneC_7', $this->ids)
            ->update(
                [
                    'total' => 0,
                ]
            );
    }
}
