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

    public function getYearlySum()
    {
        $result = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%Y') as year, SUM(amount) as total")
            ->from('expense')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->execute()
            ->fetchAll();

        return array_column($result, 'total', 'year');
    }

    public function getMonthlySum(int $year)
    {
        $result = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%m') as month, SUM(amount) as total")
            ->from('expense')
            ->where("date BETWEEN ? and ?")
            ->groupBy('month')
            ->orderBy('date', 'ASC')
            ->setParameter(0, $year . '-01-01')
            ->setParameter(1, $year . '-12-31')
            ->execute()
            ->fetchAll();

        return array_column($result, 'total', 'month');
    }
}
