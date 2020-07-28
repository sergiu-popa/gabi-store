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

    public function findLastBeforeDate(\DateTime $date)
    {
        return $this->createQueryBuilder('b')
            ->where('b.date < :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('b.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByDay(\DateTime $date)
    {
        return $this->createQueryBuilder('b')
            ->where('b.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFromDate($date)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.date > :date')
            ->setParameter('date', $date)
            ->orderBy('b.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
