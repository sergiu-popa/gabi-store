<?php

namespace App\Repository;

use App\Entity\MerchandisePayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MerchandisePayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method MerchandisePayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method MerchandisePayment[]    findAll()
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
        $results = $this->conn
            ->executeQuery("SELECT DATE_FORMAT(date, '%Y') as year, SUM(amount) as total, type " .
                "FROM merchandise_payment GROUP BY year, type ORDER BY year DESC")
            ->fetchAll();

        $payments = [];

        foreach ($results as $row) {
            $type = $row['type'];
            $year = $row['year'];

            $payments[$year][$type] = $row['total'];
        }

        return $payments;
    }
}
