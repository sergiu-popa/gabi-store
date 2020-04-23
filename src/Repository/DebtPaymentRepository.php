<?php

namespace App\Repository;

use App\Entity\DebtPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DebtPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method DebtPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method DebtPayment[]    findAll()
 * @method DebtPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebtPayment::class);
    }
}
