<?php

namespace App\Repository;

use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Provider;
use App\Entity\ProviderDebt;
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

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchandisePayment::class);
        $this->conn = $registry->getConnection();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findForDateAndProvider(Merchandise $merchandise): ?MerchandisePayment
    {
        return $this->createQueryBuilder('p')
            ->where('p.date = :today')
            ->setParameter('today', $merchandise->getDate())
            ->andWhere('p.provider = :provider')
            ->setParameter('provider', $merchandise->getProvider())
            ->andWhere('p.paymentType = :type')
            ->setParameter('type', $merchandise->getPaymentType())
            ->andWhere('p.deletedAt IS NULL')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByDay(\DateTime $date)
    {
        return $this->createQueryBuilder('p')
            ->select('p, provider')
            ->join('p.provider', 'provider')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('provider.name', 'ASC')
            ->getQuery()
            ->getResult();
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
            ->select("DATE_FORMAT(date, '%Y') as year, SUM(amount) as total, payment_type as type")
            ->from('merchandise_payment')
            ->andWhere('deleted_at is NULL')
            ->groupBy('year, type')
            ->orderBy('year', 'DESC')
            ->execute()
            ->fetchAll();

        return $this->reindex($results, 'year');
    }

    public function getMonthlySum(int $year)
    {
        $results = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%m') as month, SUM(amount) as total, payment_type as type")
            ->from('merchandise_payment')
            ->where("date BETWEEN ? and ?")
            ->andWhere('deleted_at is NULL')
            ->groupBy('month, payment_type')
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
