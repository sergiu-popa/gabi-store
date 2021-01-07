<?php

namespace App\Repository;

use App\Entity\Merchandise;
use App\Entity\Provider;
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
            ->where('d.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findForDateAndProvider(Merchandise $merchandise): ?ProviderDebt
    {
        return $this->createQueryBuilder('d')
            ->where('d.date = :today')
            ->setParameter('today', $merchandise->getDate())
            ->andWhere('d.provider = :provider')
            ->setParameter('provider', $merchandise->getProvider())
            ->andWhere('d.deletedAt IS NULL')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findForProvider(Provider $provider): array
    {
        return $this->createQueryBuilder('d')
            ->select('d, p')
            ->leftJoin('d.payments', 'p')
            ->andWhere('d.provider = :provider')
            ->setParameter('provider', $provider)
            ->andWhere('d.deletedAt IS NULL')
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
