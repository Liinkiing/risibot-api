<?php

namespace App\Repository;

use App\Entity\Risipic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Risipic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Risipic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Risipic[]    findAll()
 * @method Risipic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisipicRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Risipic::class);
    }

    /**
     * @return Risipic[]|null
     */
    public function findBySearchParams(array $parameters)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.tags LIKE :search')
            ->setParameter('search', '%' . $parameters['q'] . '%');

        $searchTerms = array_map(function ($term) {
            return trim($term);
        }, explode(" ", $parameters['q']));

        foreach ($searchTerms as $term) {
            $qb->orWhere($qb->expr()->like('r.tags', ':tag'))
                ->setParameter('tag', '%' . $term . '%');
        }

        if (isset($parameters['tags'])) {
            $tags = array_map(function ($tag) {
                return trim($tag);
            }, explode(",", $parameters['tags']));
            foreach ($tags as $tag) {
                $qb->andWhere($qb->expr()->like('r.tags', ':tag'))
                    ->setParameter('tag', '%' . $tag . '%');
            }
        }

        if (isset($parameters['category'])) {
            $qb->innerJoin('r.category', 'category')
                ->andWhere('category.name = :cname')
                ->setParameter('cname', $parameters['category']);
        }

        return $qb->getQuery()->getResult();

    }
}
