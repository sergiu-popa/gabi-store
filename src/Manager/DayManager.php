<?php

namespace App\Manager;

use App\Entity\Balance;
use App\Entity\Day;
use App\Entity\Debt;
use App\Entity\Expense;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Entity\Provider;
use App\Entity\User;
use App\Repository\DayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DayManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var DayRepository */
    private $repository;

    /** @var Security */
    private $security;

    /** @var BalanceManager */
    private $balanceManager;

    public function __construct(
        EntityManagerInterface $em,
        DayRepository $dayRepository,
        Security $security,
        BalanceManager $balanceManager
    ) {
        $this->em = $em;
        $this->repository = $dayRepository;
        $this->security = $security;
        $this->balanceManager = $balanceManager;
    }

    public function start(Day $day)
    {
        $this->save($day);
    }

    public function end(Day $day)
    {
        $day->end();
        $this->save($day);

        $transactions = $this->getTransactions($day->getDate());
        $this->balanceManager->addBalanceForToday($transactions['totals']['balance']);
    }

    public function confirm(Day $day, UserInterface $user)
    {
        $day->confirm($user);
        $this->save($day);
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

    private function save(Day $day): void
    {
        $this->em->persist($day);
        $this->em->flush();
    }

    public function getTransactions(\DateTime $date)
    {
        $todayBalance = $this->balanceManager->findForDate($date);
        $previousBalance = $this->balanceManager->findPrevious($date);

        $transactions = [
            'payments' => $this->em->getRepository(MerchandisePayment::class)->findByDay($date),
            'expenses' => $this->em->getRepository(Expense::class)->findByDay($date),
            'money' => $this->em->getRepository(Money::class)->findByDay($date),
            'providers' => $this->em->getRepository(Provider::class)->findByDay($date),
            'debts' => $this->em->getRepository(Debt::class)->findByDay($date),
        ];

        $transactions['totals'] = $this->calculateTotals($previousBalance, $transactions, $todayBalance);

        return $transactions;
    }

    // TODO refactor this using illuminate/collections (dev?)
    private function calculateTotals(Balance $previousBalance, array $transactions, Balance $todayBalance = null): array
    {
        $payments_bill =  $this->filterBy($transactions['payments'], MerchandisePayment::TYPE_BILL);
        $payments_invoice =  $this->filterBy($transactions['payments'], MerchandisePayment::TYPE_INVOICE);

        $totals = [
            'expenses' => $this->calculateTotal($transactions['expenses']),
            'money' => $this->calculateTotal($transactions['money']),
            'payments_bill' => $this->calculateTotal($payments_bill),
            'payments_invoice' => $this->calculateTotal($payments_invoice)
        ];

        $totalDay = array_sum($totals);

        $totals = array_merge($totals, $this->calculateTotalMerchandise($transactions['providers']));

        $totals['day'] = $totalDay;
        $totals['balance_previous'] = $previousBalance->getAmount();

        if($todayBalance) {
            $totals['balance'] = $todayBalance->getAmount();
        } else {
            $totals['balance'] = $previousBalance->getAmount() + $totals['merchandise'] - $totalDay;
        }

        return $totals;
    }

    private function calculateTotal(array $items, string $property = 'amount'): float
    {
        $total = 0.0;

        foreach($items as $item) {
            $total += $item->{'get'.ucfirst($property)}();
        }

        return $total;
    }

    private function filterBy(array $items, int $value, string $property = 'paymentType'): array
    {
        $filtered = [];

        foreach ($items as $item) {
            if($item->{'get'.ucfirst($property)}() === $value) {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    /**
     * @param Provider[] $providers
     */
    private function calculateTotalMerchandise(array $providers): array
    {
        $total = 0;
        $profit = 0;

        foreach ($providers as $provider) {
            $total += $provider->totalExitAmount();
            $profit += $provider->totalGrossProfit();
        }

        return [
            'merchandise' => $total,
            'profit' => $profit
        ];
    }
}
