<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected Client $client;

    public function __construct()
    {
        $clientBuilder = ClientBuilder::create();

        // Если есть cloud_id (для Elastic Cloud)
        if (config('elasticsearch.cloud_id')) {
            $clientBuilder->setElasticCloudId(config('elasticsearch.cloud_id'));
        } else {
            // Локальная конфигурация
            $host = config('elasticsearch.scheme') . '://' . config('elasticsearch.host');
            $clientBuilder->setHosts([$host]);
        }

        // Аутентификация
        if (config('elasticsearch.api_key')) {
            $clientBuilder->setApiKey(config('elasticsearch.api_key'));
        } elseif (config('elasticsearch.user') && config('elasticsearch.pass')) {
            $clientBuilder->setBasicAuthentication(
                config('elasticsearch.user'),
                config('elasticsearch.pass')
            );
        }

        $this->client = $clientBuilder->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function indexDocument(string $index, array $document, string $id = null): array
    {
        $params = [
            'index' => $index,
            'body'  => $document,
        ];

        if ($id) {
            $params['id'] = $id;
        }

        return $this->client->index($params)->asArray();
    }

    public function search(string $index, array $query): array
    {
        $params = [
            'index' => $index,
            'body'  => $query,
        ];

        return $this->client->search($params)->asArray();
    }

    public function createProductIndex(): array
    {
        $params = [
            'index' => 'products',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'filter' => [
                            'russian_stop' => [
                                'type' => 'stop',
                                'stopwords' => '_russian_'
                            ],
                            'russian_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'russian'
                            ]
                        ],
                        'analyzer' => [
                            'default' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'russian_stop',
                                    'russian_stemmer'
                                ]
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'default'
                        ],
                        'description' => [
                            'type' => 'text',
                            'analyzer' => 'default'
                        ],
                        'price' => ['type' => 'float'],
                        'qty' => ['type' => 'integer'],
                        'barcode' => ['type' => 'keyword'],
                        'category_id' => ['type' => 'integer'],
                        'brand_id' => ['type' => 'integer'],
                        'main_page' => ['type' => 'boolean'],
                        'created_at' => ['type' => 'date'],
                        'updated_at' => ['type' => 'date']
                    ]
                ]
            ]
        ];

        try {
            return $this->client->indices()->create($params)->asArray();
        } catch (\Exception $e) {
            // Если индекс уже существует, сначала удаляем его
            if (strpos($e->getMessage(), 'index_exists_exception') !== false) {
                $this->client->indices()->delete(['index' => 'products']);
                return $this->client->indices()->create($params)->asArray();
            }
            throw $e;
        }
    }
}
