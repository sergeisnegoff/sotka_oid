<?php

namespace App\Services\PageParser;

use App\Services\PageParser\PoiskManager;

class PageParserManager
{
    public function __construct()
    {
    }

    public function getParser(): PageParserInterface
    {
//        return match ($this->hash) {
//            'bfc95980634bf529e8a406db2c842b31' => new PoiskManager(),
//        };

        return new PoiskManager();
    }
}
