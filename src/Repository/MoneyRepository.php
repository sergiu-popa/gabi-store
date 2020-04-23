<?php

namespace App\Repository;

use App\Entity\Money;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Money|null find($id, $lockMode = null, $lockVersion = null)
 * @method Money|null findOneBy(array $criteria, array $orderBy = null)
 * @method Money[]    findAll()
 * @method Money[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoneyRepository extends ServiceEntityRepository
{
    use FilterTrait;

    /** @var Connection */
    private $conn;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Money::class);
        $this->conn = $registry->getConnection();
    }

    /**
     * @return Money[] Returns an array of Money objects
     */
    public function getForYearAndMonth(int $year, string $month)
    {
        $qb = $this->createQueryBuilder('m');
        $this->applyYearAndMonthFilter($year, $month, $qb, 'm');

        return $qb->getQuery()->getResult();
    }

    // SELECT DATE_FORMAT(date, '%M') as month, SUM(amount) as total FROM money WHERE date > '2020-01-01' GROUP BY month ORDER BY date ASC;
    public function getYearlySum()
    {
        $result = $this->conn
            ->executeQuery("SELECT DATE_FORMAT(date, '%Y') as year, SUM(amount) as total FROM money GROUP BY year ORDER BY year DESC")
            ->fetchAll();

        return array_column($result, 'total', 'year');
    }
}
