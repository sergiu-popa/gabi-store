<?php

namespace App\Repository;

use App\Entity\MerchandisePayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MerchandisePayment::class);
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
}
