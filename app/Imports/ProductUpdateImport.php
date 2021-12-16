<?php

namespace App\Imports;

use App\Console\Kernel;
use App\Jobs\ProcessCleanTotalJob;
use App\Jobs\ProcessUpdateFinishedJob;
use App\Jobs\ProcessUpdateJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductUpdateImport implements ToCollection {
    use Importable, RemembersRowNumber, SerializesModels;

    public function collection(Collection $collection) {

        $data = [];

        $main_category = $category = null;
        $last_category_index = 0;

        $ids = [];

        $collection->each(
            function (Collection $row, int $key) use (
                &$last_category_index,
                &$main_category,
                &$category,
                &$data,
                &$ids
            ) {
                if ($key < 5) {
                    return;
                }

                if (is_null($row[7] ?? null)) {
                    if ($last_category_index + 1 === $key) {
                        $main_category = $category;
                    }
                    $category = $row[0];
                    $last_category_index = $key;
                } else {
                    if (!isset($data[$key = "$main_category => $category"])) {
                        $data[$key] = compact('main_category', 'category') + ['items' => []];
                    }
                    $ids[] = $row[7];
                    $data[$key]['items'][] = [
                        'xml_id' => $row[7],
                        'name' => $row[0],
                        'cost' => $row[2],
                        'total' => $row[5],
                        'multiplicity' => $row[6],
                        'manufacturer' => $row[8] ?? null,
                        'image' => $row[9] ?? null,
                        'filters' => $row[10] ?? null,
                    ];
                }
            }
        );

        if (count($data) && count($ids)) {
            $uuid = Str::orderedUuid()->toString();
            Log::channel('import')->info('started', compact('uuid'));

            ProcessCleanTotalJob::dispatch($ids)
                ->onConnection(Kernel::CONNECTION_DB)
                ->onQueue(Kernel::QUEUE_IMPORT);

            foreach ($data as $item) {
                ProcessUpdateJob::dispatch($item['main_category'], $item['category'], $item['items'])
                    ->onConnection(Kernel::CONNECTION_DB)
                    ->onQueue(Kernel::QUEUE_IMPORT);
            }

            ProcessUpdateFinishedJob::dispatch($uuid)
                ->onConnection(Kernel::CONNECTION_DB)
                ->onQueue(Kernel::QUEUE_IMPORT);
        }

    }

    public static function make() {
        return new static;
    }

}
