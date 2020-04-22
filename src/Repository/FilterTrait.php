<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

trait FilterTrait
{
    private function applyYearAndMonthFilter(int $year, string $month, QueryBuilder $qb, $prefix)
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (new \DateTime("$year-$month-01"))
            ->modify('last day of');

        $qb->andWhere($prefix . '.date BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);
    }
}
