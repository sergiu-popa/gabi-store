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

    /**
     * @return Balance[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.deletedAt IS NULL')
            ->orderBy('b.date', 'DESC')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
    }
}
