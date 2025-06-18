<?php

namespace App\Traits;

use App\Services\ElasticsearchService;

trait Searchable
{
    public static function bootSearchable()
    {
        static::created(function ($model) {
            $model->addToIndex();
        });

        static::updated(function ($model) {
            $model->updateInIndex();
        });

        static::deleted(function ($model) {
            $model->removeFromIndex();
        });
    }

    public function addToIndex()
    {
        $elasticsearch = app(ElasticsearchService::class);

        $elasticsearch->indexDocument(
            $this->getSearchIndex(),
            $this->toSearchArray()
        );
    }

    public function updateInIndex()
    {
        $elasticsearch = app(ElasticsearchService::class);

        $elasticsearch->indexDocument(
            $this->getSearchIndex(),
            $this->toSearchArray(),
            $this->getKey()
        );
    }

    public function removeFromIndex()
    {
        $elasticsearch = app(ElasticsearchService::class);

        $elasticsearch->getClient()->delete([
            'index' => $this->getSearchIndex(),
            'id'    => $this->getKey(),
        ]);
    }

    public function getSearchIndex(): string
    {
        return $this->getTable();
    }

    public function toSearchArray(): array
    {
        return $this->toArray();
    }
}
