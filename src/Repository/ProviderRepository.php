<?php

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Provider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findAll()
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends ServiceEntityRepository
{
    use FilterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function findByDay(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('p')
            ->select('p, m, c')
            ->join('p.merchandises', 'm')
            ->join('m.category', 'c')
            ->where('m.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->andWhere('m.deletedAt IS NULL')
            ->orderBy('p.name', 'ASC')
            ->addOrderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
