<?php

namespace App\Controller;

use App\Risific\RisificClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/risific")
 */
class RisificController extends Controller
{

    protected $client;

    public function __construct(RisificClient $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/random", name="api.risific.random")
     * @Method({"GET"})
     */
    public function randomAction(): JsonResponse
    {
        return $this->json(
            $this->client->getRandomRisific()
        );
    }

    /**
     * @Route("/all", name="api.risific.all")
     * @Method({"GET"})
     */
    public function allAction(): JsonResponse
    {
        return $this->json(
            $this->client->getRisifics()
        );
    }

}