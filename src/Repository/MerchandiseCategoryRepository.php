<?php

namespace App\Repository;

use App\Entity\MerchandiseCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MerchandiseCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MerchandiseCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MerchandiseCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MerchandiseCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchandiseCategory::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.deletedAt IS NULL')
            ->orderBy('c.code', 'ASC');
    }

    /**
     * @return MerchandiseCategory[] Returns an array of MerchandiseCategory objects
     */
    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->select('c as category, COUNT(m) as total')
            ->leftJoin('c.merchandise', 'm')
            ->andWhere('c.deletedAt IS NULL')
            ->orderBy('c.name', 'ASC')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}
