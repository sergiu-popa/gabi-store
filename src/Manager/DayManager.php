<?php

namespace App\Manager;

use App\Entity\Day;
use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Repository\DayRepository;
use Doctrine\ORM\EntityManagerInterface;

class DayManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var DayRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em, DayRepository $dayRepository)
    {
        $this->em = $em;
        $this->repository = $dayRepository;
    }

    public function start(Day $day)
    {
        $this->em->persist($day);
        $this->em->flush();
    }

    public function getCurrentDay(): ?Day
    {
        return $this->repository->getToday();
    }

    public function getLastDay()
    {
        return $this->repository->getLastDay();
    }

    public function getTransactionsForLastDay()
    {
        return $this->getTransactions($this->getLastDay()->getDate());
    }

    public function getTransactions(\DateTime $date)
    {
        return [
            'totals' => [],
            'payments' => $this->em->getRepository(MerchandisePayment::class)->findByDay($date),
            'expenses' => $this->em->getRepository(Expense::class)->findByDay($date),
            'money' => $this->em->getRepository(Money::class)->findByDay($date),
            'merchandise' => $this->em->getRepository(Merchandise::class)->findByDay($date),
            'debts' => '' // TODO
        ];
    }
}
