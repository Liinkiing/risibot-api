<?php

namespace App\Risibank;

use App\Entity\Category;
use App\Entity\Risipic;
use App\Repository\CategoryRepository;
use App\Repository\RisipicRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class RisibankClient
{

    const BASE_URL = 'https://api.risibank.fr/api/v0';

    protected $client;
    protected $manager;
    protected $risipicRepository;
    protected $categoryRepository;

    public function __construct(EntityManagerInterface $manager,
                                RisipicRepository $risipicRepository,
                                CategoryRepository $categoryRepository
    )
    {
        $this->client = new Client();
        $this->manager = $manager;
        $this->risipicRepository = $risipicRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param string $name
     * @return Risipic[]|null
     */
    public
    function searchPicturesByName(array $parameters): ?array
    {

        $results = \GuzzleHttp\json_decode($this->client->post(self::BASE_URL . '/search', [
            'json' => ['search' => $parameters['q']]
        ])->getBody()->getContents(), true)['stickers'];

        foreach ($results as $sticker) {
            if (!$this->risipicRepository->findOneBy(['url' => $sticker['risibank_link']])) {
                $risipic = (new Risipic())
                    ->setUrl($sticker['risibank_link'])
                    ->setExtension($sticker['ext'])
                    ->setTags(explode(' ', $sticker['tags']))
                    ->setCategory(
                        $this->categoryRepository->findOneBy(['name' => $sticker['cat']]) ?? (new Category())->setName($sticker['cat'])
                    );
                $this->manager->persist($risipic);
                $this->manager->flush();
            }
        }

        $posts = $this->risipicRepository->findBySearchParams($parameters);


        return $posts;
    }


}