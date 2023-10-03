<?php

namespace App\Services\PageParser;

use App\Services\PageParser\PageParserInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class PoiskManager implements PageParserInterface
{
    private $crawler = null;

    /**
     * @throws GuzzleException
     */
    public function getHtml(string $url): self
    {
        $client = new Client();
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() === 200) {
            $html = $response->getBody()->getContents();

            $url_info = parse_url($url);

            $this->crawler = new Crawler($html, "https://".$url_info['host']);
        }

        return $this;
    }

    public function getImage(): ?string
    {
        if (is_null($this->crawler)) {
            return null;
        }

        $imageSrc = $this->crawler->filter('.offers_img.wof > link')->attr('href');

        return $this->crawler->getUri() . '/' . ltrim($imageSrc, '/');
    }

    public function getDescription(): ?string
    {
        if (is_null($this->crawler)) {
            return null;
        }

        return $this->crawler->filter('.detail_text')->text();
    }
}
