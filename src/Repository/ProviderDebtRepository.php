<?php

namespace App\Repository;

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

    public function findTodayForProvider(Provider $provider): ?ProviderDebt
    {
        return $this->createQueryBuilder('d')
            ->where('d.date = :today')
            ->setParameter('today', (new \DateTime())->setTime(0, 0, 0))
            ->andWhere('d.provider = :provider')
            ->setParameter('provider', $provider)
            ->andWhere('d.deletedAt IS NULL')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
