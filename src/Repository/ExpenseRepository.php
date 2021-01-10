<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Util\Months;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
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

    public function findByDay(\DateTime $date)
    {
        return $this->createQueryBuilder('e')
            ->select('e, c')
            ->join('e.category', 'c')
            ->where('e.deletedAt IS NULL')
            ->andWhere('e.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
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
            ->andWhere('deleted_at is NULL')
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
            ->andWhere('deleted_at is NULL')
            ->groupBy('month')
            ->orderBy('date', 'ASC')
            ->setParameter(0, $year . '-01-01')
            ->setParameter(1, $year . '-12-31')
            ->execute()
            ->fetchAll();

        return array_column($result, 'total', 'month');
    }

    public function getMonthlyCategories(int $year, array $categories)
    {
        $result = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%m') as month, category_id, SUM(amount) as total")
            ->from('expense')
            ->where("date BETWEEN ? and ?")
            ->andWhere('deleted_at is NULL')
            ->groupBy('month, category_id')
            ->orderBy('date', 'ASC')
            ->setParameter(0, $year . '-01-01')
            ->setParameter(1, $year . '-12-31')
            ->execute()
            ->fetchAll();

        $data = [];

        foreach ($result as $row) {
            $month = $row['month'];
            $category = $row['category_id'];

            $data[$month][$category] = $row['total'];

            if(isset($data['totals'][$category])) {
                $data['totals'][$category] += $row['total'];
            } else {
                $data['totals'][$category] = $row['total'];
            }
        }

        // Fill empty categories
        foreach ($data as $month => $totals) {
            foreach ($categories as $category) {
                $categoryId = $category->getId();

                if (!array_key_exists($categoryId, $totals)) {
                    $data[$month][$categoryId] = 0;
                }
            }
        }

        return $data;
    }
}
