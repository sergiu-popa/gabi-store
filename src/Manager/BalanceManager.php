<?php

namespace App\Manager;

use App\Entity\Balance;
use App\Repository\BalanceRepository;
use Doctrine\ORM\EntityManagerInterface;

class BalanceManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var BalanceRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em, BalanceRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    public function addBalanceForToday(float $amount)
    {
        $balance = new Balance();
        $balance->setDate(new \DateTime());
        $balance->setAmount($amount);

        $this->em->persist($balance);
        $this->em->flush();
    }

    public function findForTodayOrLast(\DateTime $date)
    {
        $balance = $this->repository->findByDay($date);

        if($balance === null) {
            $balance = $this->repository->findLast();
        }

        return $balance;
    }

    public function findPrevious(?\DateTime $date): Balance
    {
        $yesterday = clone $date;
        $yesterday->modify('-1 day');

        return $this->repository->findByDay($yesterday);
    }
}
