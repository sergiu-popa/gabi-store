<?php

namespace App\Repository;

use App\Entity\Provider;
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
    public function findByDate(\DateTime $date, $providerId = null): array
    {
        $date->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('s')
            ->select('s', 'a')
            ->innerJoin('s.author', 'a')
            ->orderBy('s.createdAt', 'ASC')
            ->where('s.createdAt >= :date')
            ->andWhere('s.class != :class')
            ->setParameter('date', $date)
            ->setParameter('class', 'App\Entity\Day');

        if($providerId) {
            $provider = $this->_em->getRepository(Provider::class)->find($providerId);

            $qb->andWhere('s.content LIKE :provider')
                ->setParameter('provider', '%'.$provider->getName().'%');
        }

        return $qb->getQuery()->getResult();
    }
}
