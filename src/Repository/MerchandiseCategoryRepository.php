<?php

namespace App\Repository;

use App\Entity\MerchandiseCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MerchandiseCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MerchandiseCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MerchandiseCategory[]    findAll()
 * @method MerchandiseCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MerchandiseCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchandiseCategory::class);
    }

    // /**
    //  * @return MerchandiseCategory[] Returns an array of MerchandiseCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MerchandiseCategory
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
