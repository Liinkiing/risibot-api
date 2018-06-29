<?php

namespace App\Risific;

use App\Entity\Risific;
use App\Repository\RisificRepository;
use App\Utils\Arr;
use Doctrine\ORM\EntityManagerInterface;
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
    protected $manager;
    protected $repository;

    public function __construct(EntityManagerInterface $manager, RisificRepository $repository, CacheInterface $cache)
    {
        $this->client = new Client();
        $this->cache = $cache;
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function getRisifics(): array
    {
        if(!$this->cache->has(self::CACHE_KEY)) {
            $crawler = $this->client->request("GET", self::BASE_URL);
            $crawler->filter('#lcp_instance_0 li a')->each(function (Crawler $node) {
                $url = $node->attr('href');
                $crawler = $this->client->request("GET", $url);
                $images = $crawler->filter('#content article img:first-of-type');
                $thumbnail = $images->count() > 0 ? $images->first()->attr('src') : 'https://i2.wp.com/image.noelshack.com/minis/2016/51/1482448857-celestinrisitas.png?resize=68%2C51&ssl=1';
                if(!$this->repository->findOneBy(['url' => $url])) {
                    $fic = (new Risific())
                        ->setThumbnail($thumbnail)
                        ->setUrl($url)
                        ->setTitle($node->text());
                    $this->manager->persist($fic);
                }
            });
            $this->manager->flush();
            $fics = $this->repository->findAll();
            $this->cache->set(self::CACHE_KEY, $fics, self::CACHE_TTL);
        } else {
            $fics = $this->cache->get(self::CACHE_KEY);
        }

        return $fics;
    }

    public function getRandomRisific(): Risific
    {
        return Arr::random($this->getRisifics());
    }

}