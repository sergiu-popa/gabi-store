<?php

namespace App\Repository;

use App\Entity\Snapshot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Snapshot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Snapshot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Snapshot[]    findAll()
 * @method Snapshot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snapshot::class);
    }

    /**
     * @return Snapshot[]
     */
    public function findByDate(\DateTime $date): array
    {
        $date->setTime(0, 0, 0);

        return $this->createQueryBuilder('s')
            ->select('s', 'a')
            ->innerJoin('s.author', 'a')
            ->orderBy('s.createdAt', 'DESC')
            ->where('s.createdAt >= :date')
            ->andWhere('s.class != :class')
            ->setParameter('date', $date)
            ->setParameter('class', 'App\Entity\Day')
            ->getQuery()
            ->getResult();
    }
}
