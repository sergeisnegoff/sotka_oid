<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;

class ConfigureMeilisearch extends Command
{
    protected $signature = 'meilisearch:configure';
    protected $description = 'Настраивает индексы Meilisearch для оптимального поиска';

    public function handle()
    {
        $host = config('scout.meilisearch.host');
        $key = config('scout.meilisearch.key');

        if (!$host) {
            $this->error('Хост Meilisearch не настроен');
            return 1;
        }

        try {
            $client = new Client($host, $key);
            $index = $client->index('products');

            // Основные настройки
            $settings = [
                'searchableAttributes' => ['title', 'title_raw', 'description'],
                'displayedAttributes' => ['id', 'title', 'description', 'price', 'category_id', 'brand_id', 'images', 'total'],
                'filterableAttributes' => ['category_id', 'brand_id', 'total'],
                'sortableAttributes' => ['title', 'price', 'created_at'],

                // Настройки для поиска по подстроке
                'typoTolerance' => [
                    'enabled' => true,
                    'minWordSizeForTypos' => [
                        'oneTypo' => 2,
                        'twoTypos' => 3
                    ],
                ],

                // Обработка специальных символов
                'separatorTokens' => [' ', ',', '.', '-', '_', '/', '*'],

                // Синонимы для улучшения поиска

                // Настройки пагинации
                'pagination' => [
                    'maxTotalHits' => 500
                ],

                // Правила ранжирования
                'rankingRules' => [
                    'words',
                    'typo',
                    'proximity',
                    'attribute',
                    'sort',
                    'exactness'
                ]
            ];

            // Обновляем настройки
            $index->updateSettings($settings);
            $this->info('✅ Настройки Meilisearch успешно обновлены!');

            // Переиндексация
            if ($this->confirm('🔁 Выполнить переиндексацию продуктов?', true)) {
                $this->call('scout:flush', ['model' => 'App\\Models\\Product']);
                $this->call('scout:import', ['model' => 'App\\Models\\Product']);
                $this->info('Индекс продуктов перестроен!');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Ошибка: ' . $e->getMessage());
            $this->line('Проверьте:');
            $this->line('- Доступен ли сервер Meilisearch по адресу ' . $host);
            $this->line('- Правильно ли указан API-ключ (если используется)');
            return 1;
        }
    }
}
