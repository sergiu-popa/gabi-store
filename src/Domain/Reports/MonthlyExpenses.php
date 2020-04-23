<?php

namespace App\Domain\Reports;

use App\Domain\Month;

class MonthlyExpenses
{
    /** @var DailyCategoryExpenses[] */
    private $days;

    public function __construct($year, $month)
    {
        $this->days = Month::generateDays($year, $month, DailyCategoryExpenses::class);
    }

    public function addExpensesInEachDay($expenses)
    {
        foreach ($expenses as $expense) {
            $day = $expense->getDate()->format('j');
            $this->days[$day]->addExpense($expense);
        }
    }

    /**
     * @return DailyCategoryExpenses[]
     */
    public function getDays(): array
    {
        return $this->days;
    }
}
