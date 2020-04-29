<?php

namespace App\Repository;

use App\Entity\Debt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Debt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Debt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Debt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Debt::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('d')
            ->select('d, p')
            ->join('d.provider', 'p')
            ->where('d.paidFully = :paid')
            ->setParameter('paid', false)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
