<?php

namespace App\Manager;

use App\Entity\Day;
use App\Entity\Debt;
use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Entity\User;
use App\Repository\DayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DayManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var DayRepository */
    private $repository;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, DayRepository $dayRepository, Security $security)
    {
        $this->em = $em;
        $this->repository = $dayRepository;
        $this->security = $security;
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

    public function getDay(\DateTime $date)
    {
        return $this->repository->getByDay($date);
    }

    public function getTransactionsForLastDay()
    {
        return $this->getTransactions($this->getLastDay()->getDate());
    }

    /**
     * A normal user can modify the current day or the last one.
     * An administrator can modify anything.
     */
    public function userCanModifyDay(\DateTime $currentDate)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if($user->isAdmin()) {
            return true;
        }

        // Today is the current day
        if ($currentDate->format('Y-m-d') === (new \DateTime())->format('Y-m-d')) {
            return true;
        }

        // Last day is the current day
        if($currentDate->format('Y-m-d') === $this->getLastDay()->getDate()->format('Y-m-d')) {
            return true;
        }

        return false;
    }

    public function getTransactions(\DateTime $date)
    {
        return [
            'totals' => [],
            'payments' => $this->em->getRepository(MerchandisePayment::class)->findByDay($date),
            'expenses' => $this->em->getRepository(Expense::class)->findByDay($date),
            'money' => $this->em->getRepository(Money::class)->findByDay($date),
            'merchandises' => $this->em->getRepository(Merchandise::class)->findByDay($date),
            'debts' => $this->em->getRepository(Debt::class)->findByDay($date),
        ];
    }
}
