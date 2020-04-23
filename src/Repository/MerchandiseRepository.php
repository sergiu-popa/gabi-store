<?php

namespace App\Repository;

use App\Entity\Merchandise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Merchandise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Merchandise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Merchandise[]    findAll()
 * @method Merchandise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MerchandiseRepository extends ServiceEntityRepository
{
    use FilterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Merchandise::class);
    }

    /**
     * @return Merchandise[]
     */
    public function getForYearAndMonth(int $year, string $month)
    {
        $qb = $this->createQueryBuilder('m');
        $this->applyYearAndMonthFilter($year, $month, $qb, 'm');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Merchandise[]
     */
    public function searchMerchandise($name)
    {
        return $this->createQueryBuilder('m')
            ->select('m, p')
            ->join('m.provider', 'p')
            ->andWhere('m.name LIKE :name')
            ->setParameter('name', "%$name%")
            ->orderBy('m.date', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }
}
