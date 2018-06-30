<?php

namespace App\Controller;

use App\Risibank\RisibankClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/risibank")
 */
class RisibankController extends ApiController
{

    const DEFAULT_SEARCH_TERM = 'celestin';

    protected $client;

    public function __construct(RisibankClient $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/search", name="api.risibank.search")
     */
    public function searchAction(Request $request): JsonResponse
    {
        $default = ['q' => $request->get('q') ?? self::DEFAULT_SEARCH_TERM];

        return $this->json(
            $this->client->searchPicturesByName(array_merge($default, $request->query->all()))
        );
    }

}