<?php

namespace App\Repository;

use App\Entity\Risific;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Risific|null find($id, $lockMode = null, $lockVersion = null)
 * @method Risific|null findOneBy(array $criteria, array $orderBy = null)
 * @method Risific[]    findAll()
 * @method Risific[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RisificRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Risific::class);
    }

    public function findOneRandom(): ?Risific
    {
        return $this->createQueryBuilder('r')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
