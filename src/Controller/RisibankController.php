<?php

namespace App\Controller;

use App\Repository\RisipicRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/risibank")
 */
class RisibankController extends ApiController
{

    const DEFAULT_SEARCH_TERM = 'risitas';

    private $repository;

    public function __construct(RisipicRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/search", name="api.risibank.search")
     */
    public function searchAction(Request $request): JsonResponse
    {
        $default = ['q' => $request->get('q') ?? self::DEFAULT_SEARCH_TERM];

        return $this->json(
            $this->repository->findBySearchParams(array_merge($default, $request->query->all()))
        );
    }

}