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

    public function getYearlySum()
    {
        $result = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%Y') as year, SUM(amount) as total")
            ->from('money')
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
            ->from('money')
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
