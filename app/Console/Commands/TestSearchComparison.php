<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use MeiliSearch\Client;

class TestSearchComparison extends Command
{
    protected $signature = 'test:search {query : Поисковый запрос (например, "укроп")}';
    protected $description = 'Сравнивает результаты поиска Meilisearch и SQL LIKE';

    public function handle()
    {
        $query = $this->argument('query');

        // 1. Получаем результаты SQL LIKE
        $sqlResults = DB::table('products')
            ->where('title', 'LIKE', "%{$query}%")
            ->select('id', 'title')
            ->get();

        // 2. Получаем результаты Meilisearch
        $meiliResults = Product::search($query)->take(1000)->raw()['hits'];

        // 3. Сравниваем
        $this->table(
            ['Тип поиска', 'Кол-во результатов', 'Примеры'],
            [
                [
                    'SQL LIKE',
                    $sqlResults->count(),
                    $sqlResults->pluck('title')->take(5)->implode(', ')
                ],
                [
                    'Meilisearch',
                    count($meiliResults),
                    collect($meiliResults)->pluck('title')->take(5)->implode(', ')
                ]
            ]
        );

        // 4. Выводим ID отсутствующих в Meilisearch
        $sqlIds = $sqlResults->pluck('id')->toArray();
        $meiliIds = collect($meiliResults)->pluck('id')->toArray();
        $missingIds = array_diff($sqlIds, $meiliIds);

        if (!empty($missingIds)) {
            $this->error('❌ Отсутствуют в Meilisearch:');
            $missingProducts = DB::table('products')
                ->whereIn('id', $missingIds)
                ->select('id', 'title')
                ->get();

            $this->table(['ID', 'Название'], $missingProducts);
        } else {
            $this->info('✅ Все результаты SQL LIKE найдены в Meilisearch!');
        }
    }
}
