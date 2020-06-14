<?php

namespace App\Domain\Reports;

use App\Domain\Month;
use App\Entity\Expense;
use App\Entity\ExpenseCategory;

class MonthlyExpenses
{
    /** @var DailyCategoryExpenses[] */
    private $days;

    /** @var array */
    private $categories;

    /** @var float */
    private $total;

    /** @var array */
    private $totalsByCategory;

    /** @var array */
    private $percentagesByCategory;

    public function __construct($year, $month, $categories, array $expenses)
    {
        $this->days = Month::generateDays($year, $month, DailyCategoryExpenses::class);
        $this->initializeByCategoryArrays($categories);
        $this->categories = $categories;
        $this->total = 0;

        $this->addExpensesInEachDay($expenses);
    }

    /**
     * @var Expense[] $expenses
     */
    private function addExpensesInEachDay(array $expenses): void
    {
        foreach ($expenses as $expense) {
            $day = $expense->getDate()->format('j');
            $this->days[$day]->addExpense($expense);

            $this->totalsByCategory[$expense->getCategory()->getId()] += $expense->getAmount();
            $this->total += $expense->getAmount();
        }
    }

    /**
     * @return DailyCategoryExpenses[]
     */
    public function getDays(): array
    {
        return $this->days;
    }

    public function getPercentages(): array
    {
        foreach($this->totalsByCategory as $categoryId => $totalCategory) {
            $this->percentagesByCategory[$categoryId] = number_format($totalCategory / $this->total * 100, 1);
        }

        return $this->percentagesByCategory;
    }

    public function getMaxPercentage(): float
    {
        return max($this->percentagesByCategory);
    }

    /**
     * @var ExpenseCategory $categories
     */
    private function initializeByCategoryArrays(array $categories): void
    {
        foreach($categories as $category) {
            $this->totalsByCategory[$category->getId()] = 0;
            $this->percentagesByCategory[$category->getId()] = 0;
        }
    }

    public function getTotalsByCategory(): array
    {
        return $this->totalsByCategory;
    }
}
