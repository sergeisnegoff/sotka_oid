<?php

namespace App\Services\PageParser;

interface PageParserInterface
{
    public function getHtml(string $url);
    public function getImage(): ?string;
    public function getDescription(): ?string;
}
