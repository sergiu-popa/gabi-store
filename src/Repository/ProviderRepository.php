<?php

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Provider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.deletedAt IS NULL')
            ->orderBy('p.name', 'ASC');
    }

    public function findAll(): array
    {
        return $this->getQueryBuilder()->getQuery()->getResult();
    }

    /**
     * Returns all debts paid fully = false.
     */
    public function findUnpaid()
    {
        return $this->createQueryBuilder('p')
            ->select('p, d')
            ->join('p.debts', 'd')
            ->where('d.paidFully = :paid')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('paid', false)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDay(\DateTimeInterface $date): array
    {
        return $this->getQueryBuilder()
            ->select('p, m, c')
            ->join('p.merchandises', 'm')
            ->join('m.category', 'c')
            ->where('m.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->addOrderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns the providers which came today: to order or to be paid.
     */
    public function findForToday(string $today): array
    {
        return $this->getQueryBuilder()
            ->andWhere('p.days LIKE :today')
            ->setParameter('today', '%'.$today.'%')
            ->getQuery()
            ->getResult();
    }
}
