<?php

namespace App\Jobs;

use App\Models\Preorder;
use App\Models\PreorderTableSheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ParsePreorderFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $preorder;

    public function __construct(Preorder $preorder)
    {
        $this->preorder = $preorder;
    }

    public function handle(): void
    {
        $file = IOFactory::load(storage_path() . '/app/public/'.json_decode($this->preorder->file)[0]->download_link);
        $sheets = $file->getAllSheets();

        foreach ($sheets as $sheet) {
            PreorderTableSheet::query()
                ->create([
                    'preorder_id' => $this->preorder->id,
                    'title' => $sheet->getTitle(),
                    'active' => 0
                ]);
        }
    }
}
