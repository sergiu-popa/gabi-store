<?php

namespace App\Domain;

use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;

class Day
{
    /** @var Money[] */
    private $money;

    /** @var Expense[] */
    private $expenses;

    /** @var Expense[] */
    private $merchandise;

    /** @var MerchandisePayment[] */
    private $merchandisePayments;

    /** @var float */
    private $totalMoney = 0.0;

    /** @var float */
    private $totalExpenses = 0.0;

    /** @var float */
    private $totalMerchandiseEnterValue = 0.0;

    /** @var float */
    private $totalMerchandiseExitValue = 0.0;

    /** @var float */
    private $totalMerchandisePaymentsInvoice = 0.0;

    /** @var float */
    private $totalMerchandisePaymentsBill = 0.0;

    public function addMoney(Money $money): void
    {
        $this->money[] = $money;

        $this->totalMoney += $money->getAmount();
    }

    public function addExpense(Expense $expense): void
    {
        $this->expenses[] = $expense;

        $this->totalExpenses += $expense->getAmount();
    }

    public function addMerchandise(Merchandise $merchandise): void
    {
        $this->merchandise[] = $merchandise;

        $this->totalMerchandiseEnterValue += $merchandise->getTotalEnterValue();
        $this->totalMerchandiseExitValue += $merchandise->getTotalExitValue();
    }

    public function addMerchandisePayments(MerchandisePayment $payment): void
    {
        $this->merchandisePayments[] = $payment;

        if ($payment->paidWithInvoice()) {
            $this->totalMerchandisePaymentsInvoice += $payment->getAmount();
        }

        if ($payment->paidWithBill()) {
            $this->totalMerchandisePaymentsBill += $payment->getAmount();
        }
    }

    public function getTotalSales(): float
    {
        return $this->totalExpenses +$this->totalMerchandisePaymentsInvoice + $this->totalMerchandisePaymentsBill
            + $this->totalMoney;
    }

    public function getTotalMoney(): float
    {
        return $this->totalMoney;
    }

    public function getTotalExpenses(): float
    {
        return $this->totalExpenses;
    }

    public function getTotalMerchandiseEnterValue(): float
    {
        return $this->totalMerchandiseEnterValue;
    }

    public function getTotalMerchandisePaymentsInvoice(): float
    {
        return $this->totalMerchandisePaymentsInvoice;
    }

    public function getTotalMerchandisePaymentsBill(): float
    {
        return $this->totalMerchandisePaymentsBill;
    }

    public function getTotalMerchandiseGrossProfit()
    {
        return $this->totalMerchandiseExitValue - $this->totalMerchandiseEnterValue;
    }
}
