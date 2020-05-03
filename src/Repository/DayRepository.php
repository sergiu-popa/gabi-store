<?php

namespace App\Repository;

use App\Entity\Day;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Day|null find($id, $lockMode = null, $lockVersion = null)
 * @method Day|null findOneBy(array $criteria, array $orderBy = null)
 * @method Day[]    findAll()
 * @method Day[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Day::class);
    }

    public function getToday(): ?Day
    {
        return $this->createQueryBuilder('d')
            ->where('d.date = :date')
            ->setParameter('date', (new \DateTime())->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastDay(): Day
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}
