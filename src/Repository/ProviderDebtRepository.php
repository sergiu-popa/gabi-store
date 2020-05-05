<?php

namespace App\Repository;

use App\Entity\ProviderDebt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProviderDebt|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderDebt|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderDebt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderDebtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderDebt::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('d')
            ->select('d, p')
            ->join('d.provider', 'p')
            ->where('d.paidFully = :paid')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('paid', false)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
