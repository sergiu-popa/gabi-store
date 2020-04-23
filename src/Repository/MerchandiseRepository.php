<?php

namespace App\Repository;

use App\Entity\Merchandise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
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

    /** @var Connection */
    private $conn;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Merchandise::class);
        $this->conn = $registry->getConnection();
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

    // SELECT DATE_FORMAT(date, '%M') as month, SUM(amount) as total, type FROM merchandise_payment WHERE date > '2020-01-01' GROUP BY month, type ORDER BY date ASC;
    public function getYearlySum()
    {
        $results = $this->conn
            ->executeQuery("
        SELECT
            DATE_FORMAT(date, '%Y') as year,
            SUM(amount * enter_price) as enterTotal,
            SUM(amount * exit_price) as exitTotal,
            SUM(amount * exit_price) - SUM(amount * enter_price) as profit
        FROM merchandise GROUP BY year ORDER BY year DESC
        ")->fetchAll();

        $merchandise = [];

        foreach ($results as $row) {
            $year = $row['year'];

            $merchandise[$year] = $row['exitTotal'] - $row['enterTotal'];
        }

        return $merchandise;
    }
}
