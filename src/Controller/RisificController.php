<?php

namespace App\Controller;

use App\Repository\RisificRepository;
use App\Risibank\RisificClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/risific")
 */
class RisificController extends ApiController
{

    protected $repository;

    public function __construct(RisificRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/random", name="api.risific.random")
     * @Method({"GET"})
     */
    public function randomAction(): JsonResponse
    {
        return $this->json(
            $this->repository->findOneRandom()
        );
    }

    /**
     * @Route("/all", name="api.risific.all")
     * @Method({"GET"})
     */
    public function allAction(): JsonResponse
    {
        return $this->json(
            $this->repository->findAll()
        );
    }

}