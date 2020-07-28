<?php

namespace App\Repository;

use App\Entity\Merchandise;
use App\Entity\Provider;
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

    public function findByDay(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->andWhere('m.deletedAt IS NULL')
            ->orderBy('m.date')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Merchandise[]
     */
    public function findAll(): array
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
    public function searchMerchandise($query, $provider = null)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m, p')
            ->join('m.provider', 'p')
            ->where('m.deletedAt is NULL')
            ->andWhere('m.name LIKE :name')
            ->setParameter('name', "%$query%");

        if($provider) {
            $qb->andWhere('m.provider = :provider')
                ->setParameter('provider', $provider);
        }

        return $qb->orderBy('m.provider')
            ->addOrderBy('m.date', 'DESC')
            ->addOrderBy('m.enterPrice', 'DESC');
    }

    public function getYearlySum($groupByCategory = false)
    {
        $qb = $this->conn->createQueryBuilder()
            ->select("DATE_FORMAT(date, '%Y') as year")
            ->addSelect('SUM(amount * enter_price) as enterTotal')
            ->addSelect('SUM(amount * exit_price) as exitTotal')
            ->from('merchandise')
            ->andWhere('deleted_at is NULL')
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
            ->andWhere('deleted_at is NULL')
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

    /**
     * Returns a similiar merchandise for the Provider today.
     */
    public function findSimilar(Provider $provider): ?Merchandise
    {
        return $this->createQueryBuilder('m')
            ->where('m.provider = :provider')
            ->setParameter('provider', $provider)
            ->andWhere('m.date = :today')
            ->andWHere('m.deletedAt IS NULL')
            ->setParameter('today', (new \DateTime())->format('Y-m-d'))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
