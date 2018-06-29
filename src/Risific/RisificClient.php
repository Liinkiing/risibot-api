<?php

namespace App\Risific;

use App\Utils\Arr;
use Goutte\Client;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\DomCrawler\Crawler;

class RisificClient
{

    const BASE_URL = 'https://risific.fr/';
    const CACHE_KEY = 'risific.posts';
    const CACHE_TTL = 86400;

    protected $client;
    protected $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->client = new Client();
        $this->cache = $cache;
    }

    public function getPosts(): array
    {
        if(!$this->cache->has(self::CACHE_KEY)) {
            $posts = [];
            $crawler = $this->client->request("GET", self::BASE_URL);
            $crawler->filter('#lcp_instance_0 li a')->each(function (Crawler $node) use (&$posts) {
                $url = $node->attr('href');
                $crawler = $this->client->request("GET", $url);
                $images = $crawler->filter('#content article img:first-of-type');
                $thumbnail = $images->count() > 0 ? $images->first()->attr('src') : 'https://i2.wp.com/image.noelshack.com/minis/2016/51/1482448857-celestinrisitas.png?resize=68%2C51&ssl=1';
                $posts[] = [
                    'title' => $node->text(),
                    'thumbnail' => $thumbnail,
                    'url' => $url
                ];
            });
            $this->cache->set(self::CACHE_KEY, $posts, self::CACHE_TTL);
        } else {
            $posts = $this->cache->get(self::CACHE_KEY);
        }

        return $posts;
    }

    public function getRandomPost()
    {
        return Arr::random($this->getPosts());
    }

}