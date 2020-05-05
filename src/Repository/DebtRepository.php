<?php

namespace App\Repository;

use App\Entity\Debt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Debt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Debt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Debt[]    findAll()
 * @method Debt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Debt::class);
    }

    /**
     * @return Debt[]
     */
    public function findByDay(\DateTime $date): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.deletedAt IS NULL')
            ->andWhere('d.date = :date')
            ->setParameter('date', $date)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
