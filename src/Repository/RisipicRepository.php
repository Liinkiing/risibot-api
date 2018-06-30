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
    public function findBySearchParams(array $parameters): ?array
    {
        $qb = $this->createQueryBuilder('r');

        $searchTerms = array_map('trim', explode(' ', $parameters['q']));

        foreach ($searchTerms as $term) {
            $qb->orWhere($qb->expr()->like('r.tags', ':tag'))
                ->setParameter('tag', '%' . $term . '%');
        }

        if (isset($parameters['tags'])) {
            $tags = array_map('trim', explode(',', $parameters['tags']));
            foreach ($tags as $tag) {
                $qb->orWhere($qb->expr()->like('r.tags', ':tag'))
                    ->setParameter('tag', '%' . $tag . '%');
            }
        }

        $qb->addOrderBy('r.views', 'DESC');

        return $qb
            ->setMaxResults(Risipic::PER_PAGE)
            ->getQuery()
            ->getResult();

    }
}
