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

    public function getYearlySum()
    {
        $results = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%Y') as year")
            ->addSelect('SUM(amount * enter_price) as enterTotal')
            ->addSelect('SUM(amount * exit_price) as exitTotal')
            ->from('merchandise')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->execute()
            ->fetchAll();

        return $this->reindex($results, 'year');
    }

    public function getMonthlySum(int $year)
    {
        $results = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%m') as month")
            ->addSelect('SUM(amount * enter_price) as enterTotal')
            ->addSelect('SUM(amount * exit_price) as exitTotal')
            ->from('merchandise')
            ->where("date BETWEEN ? and ?")
            ->groupBy('month')
            ->orderBy('date', 'ASC')
            ->setParameter(0, $year . '-01-01')
            ->setParameter(1, $year . '-12-31')
            ->execute()
            ->fetchAll();

        return $this->reindex($results, 'month');
    }

    private function reindex(array $results, string $keyPeriod)
    {
        $merchandise = [];

        foreach ($results as $row) {
            $year = $row[$keyPeriod];

            $merchandise[$year] = $row['exitTotal'] - $row['enterTotal'];
        }

        return $merchandise;
    }
}
