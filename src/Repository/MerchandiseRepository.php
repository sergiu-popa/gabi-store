<?php

namespace App\Repository;

use App\Entity\Merchandise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Merchandise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Merchandise|null findOneBy(array $criteria, array $orderBy = null)
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
    public function findAll()
    {
        // TODO pagination please
        return $this->createQueryBuilder('m')
            ->select('m, p, c')
            ->join('m.provider', 'p')
            ->join('m.category', 'c')
            ->setMaxResults(25)
            ->orderBy('m.date', 'DESC')
            ->getQuery()
            ->getResult();
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

    public function getYearlySum($groupByCategory = false)
    {
        $qb = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%Y') as year")
            ->addSelect('SUM(amount * enter_price) as enterTotal')
            ->addSelect('SUM(amount * exit_price) as exitTotal')
            ->from('merchandise')
            ->groupBy('year')
            ->orderBy('year', 'DESC');

        if ($groupByCategory) {
            $qb->addSelect('category_id')
                ->addGroupBy('category_id');
        }

        // TODO redo this with collections and move outside?
        $results = $qb->execute()->fetchAll();

        if($groupByCategory) {
            return $this->groupByCategoryAndReindex($results, 'year');
        }

        return $this->calculateProfitAndReindex($results, 'year');
    }

    public function getMonthlySum(int $year, $groupByCategory = false)
    {
        $qb = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%m') as month")
            ->addSelect('SUM(amount * enter_price) as enterTotal')
            ->addSelect('SUM(amount * exit_price) as exitTotal')
            ->from('merchandise')
            ->where("date BETWEEN ? and ?")
            ->groupBy('month')
            ->orderBy('date', 'ASC')
            ->setParameter(0, $year . '-01-01')
            ->setParameter(1, $year . '-12-31');

        if ($groupByCategory) {
            $qb->addSelect('category_id')
                ->addGroupBy('category_id');
        }

        // TODO redo this with collections and move outside?
        $results = $qb->execute()->fetchAll();

        if($groupByCategory) {
            return $this->groupByCategoryAndReindex($results, 'month');
        }

        return $this->calculateProfitAndReindex($results, 'month');
    }

    private function calculateProfitAndReindex(array $results, string $keyPeriod)
    {
        $merchandise = [];

        foreach ($results as $row) {
            $key = $row[$keyPeriod];

            $merchandise[$key] = $row['exitTotal'] - $row['enterTotal'];
        }

        return $merchandise;
    }

    private function groupBy(array $results, string $key)
    {
        $merchandise = [];

        foreach ($results as $row) {
            $keyValue = $row[$key];
            $merchandise[$keyValue][] = $row;
        }

        return $merchandise;
    }

    private function groupByCategoryAndReindex(array $results, string $key)
    {
        $months = $this->groupBy($results, $key);

        $data = [];
        foreach ($months as $month => $categories) {
            $data[$month] = $this->calculateProfitAndReindex($categories, 'category_id');
        }

        return $data;
    }

    public function findByDay(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('m')
            ->select('m, p, c')
            ->join('m.provider', 'p')
            ->join('m.category', 'c')
            ->where('m.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
