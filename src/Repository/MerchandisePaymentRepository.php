<?php

namespace App\Repository;

use App\Entity\MerchandisePayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MerchandisePayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method MerchandisePayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method MerchandisePayment[]    findAll()
 * @method MerchandisePayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MerchandisePaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchandisePayment::class);
    }

    // /**
    //  * @return MerchandisePayment[] Returns an array of MerchandisePayment objects
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
    public function findOneBySomeField($value): ?MerchandisePayment
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
