<?php

namespace App\Domain\Reports;

use App\Entity\ExpenseCategory;
use App\Entity\Expense;

class DailyCategoryExpenses
{
    /** @var ExpenseCategory[] */
    private $categories = [];

    /**
     * Adds the expenses to its corresponding category in array.
     */
    public function addExpense(Expense $expense)
    {
        $categoryId = $expense->getCategory()->getId();

        if (isset($this->categories[$categoryId]) === false) {
            $this->categories[$categoryId] = 0;
        }

        $this->categories[$categoryId] += $expense->getAmount();
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}
