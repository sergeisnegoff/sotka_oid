<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\ElasticsearchService;

class IndexProducts extends Command
{
    protected $signature = 'elasticsearch:index-products';
    protected $description = 'Index all products to Elasticsearch';

    public function handle()
    {
        $elasticsearch = app(ElasticsearchService::class);
        if (!$elasticsearch->getClient()->ping()->asBool()) {
            $this->error('Cannot connect to Elasticsearch!');
            return 1;
        }
        // Создаем индекс
        $this->info('Creating products index...');
        $elasticsearch->createProductIndex();

        // Индексируем продукты
        $this->info('Indexing products...');

        $count = 0;
        Product::chunk(200, function ($products) use ($elasticsearch, &$count) {
            foreach ($products as $product) {
                $product->addToIndex();
                $count++;
            }
            $this->info("Indexed {$count} products...");
        });

        $this->info("Done! Total indexed: {$count} products");
    }
}
