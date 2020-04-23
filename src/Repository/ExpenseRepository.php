<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    use FilterTrait;

    /** @var Connection */
    private $conn;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
        $this->conn = $registry->getConnection();
    }

    /**
     * @return Expense[]
     */
    public function getForYearAndMonth(int $year, string $month)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e, c')
            ->join('e.category', 'c');

        $this->applyYearAndMonthFilter($year, $month, $qb, 'e');

        return $qb->getQuery()->getResult();
    }

    // SELECT DATE_FORMAT(date, '%M') as month, SUM(amount) as total FROM expense WHERE date > '2020-01-01' GROUP BY month ORDER BY date ASC;
    public function getYearlySum()
    {
        $result = $this->conn
            ->executeQuery("SELECT DATE_FORMAT(date, '%Y') as year, SUM(amount) as total FROM expense GROUP BY year ORDER BY year DESC")
            ->fetchAll();

        return array_column($result, 'total', 'year');
    }
}
