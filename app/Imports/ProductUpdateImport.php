<?php

namespace App\Imports;

use App\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProductUpdateImport implements ToModel, WithChunkReading, /*ShouldQueue, */WithUpserts, WithBatchInserts {
    use Importable, RemembersRowNumber, SerializesModels;

    public $now;

    public function __construct(Carbon $now) {
        $this->now = $now;
    }

    public function model(array $row) {
        if ($this->getRowNumber() < 7) {
            return null;
        }

        if (is_null($row[7] ?? null)) {
            echo $this->getRowNumber() . ': ' . $row[0];
            echo  "\n";
            return null;
        }else{
            echo sprintf('%s: %s, %s, %s, %s, %s, %s, %s', $this->getRowNumber(), $row[0], $row[1], $row[2], $row[5], $row[6], $row[7], $row[8] ?? '');
            echo  "\n";
        }

        return null;
        return new Product([]);
    }

    public function chunkSize(): int {
        return 100;
    }

    public function batchSize(): int {
        return 100;
    }

    public function uniqueBy() {
        return 'oneC_7';
    }

    public static function make() {
        return new static(now());
    }
}
