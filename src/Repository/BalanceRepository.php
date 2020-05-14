<?php

namespace App\Repository;

use App\Entity\Balance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Balance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Balance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Balance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BalanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Balance::class);
    }

    public function findByDay(\DateTime $date)
    {
        return $this->createQueryBuilder('b')
            ->where('b.deletedAt IS NULL')
            ->andWhere('b.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getSingleResult();
    }
}
