<?php

namespace App\Repository;

use App\Entity\MerchandisePayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MerchandisePayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method MerchandisePayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method MerchandisePayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MerchandisePaymentRepository extends ServiceEntityRepository
{
    use FilterTrait;

    /** @var Connection */
    private $conn;

    public function findByDay(\DateTime $date)
    {
        return $this->createQueryBuilder('p')
            ->select('p, provider')
            ->join('p.provider', 'provider')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchandisePayment::class);
        $this->conn = $registry->getConnection();
    }

    /**
     * @return MerchandisePayment[]
     */
    public function getForYearAndMonth(int $year, string $month, $provider = null)
    {
        $qb = $this->createQueryBuilder('m');

        if ($provider !== null) {
            $qb->andWhere('m.provider = :provider')
                ->setParameter('provider', $provider);
        }

        $this->applyYearAndMonthFilter($year, $month, $qb, 'm');

        return $qb->getQuery()->getResult();
    }

    // SELECT DATE_FORMAT(date, '%M') as month, SUM(amount) as total, type FROM merchandise_payment WHERE date > '2020-01-01' GROUP BY month, type ORDER BY date ASC;
    public function getYearlySum()
    {
        $results = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%Y') as year, SUM(amount) as total, type")
            ->from('merchandise_payment')
            ->groupBy('year, type')
            ->orderBy('year', 'DESC')
            ->execute()
            ->fetchAll();

        return $this->reindex($results, 'year');
    }

    public function getMonthlySum(int $year)
    {
        $results = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%m') as month, SUM(amount) as total, type")
            ->from('merchandise_payment')
            ->where("date BETWEEN ? and ?")
            ->groupBy('month, type')
            ->orderBy('date', 'ASC')
            ->setParameter(0, $year . '-01-01')
            ->setParameter(1, $year . '-12-31')
            ->execute()
            ->fetchAll();

        return $this->reindex($results, 'month');
    }

    private function reindex(array $results, $periodKey)
    {
        $payments = [];

        foreach ($results as $row) {
            $type = $row['type'];
            $year = $row[$periodKey];

            $payments[$year][$type] = $row['total'];
        }

        return $payments;
    }
}
