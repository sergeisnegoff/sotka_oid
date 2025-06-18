<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'localhost:9200'),
    'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
    'user' => env('ELASTICSEARCH_USER'),
    'pass' => env('ELASTICSEARCH_PASS'),
];
