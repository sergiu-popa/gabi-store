<?php

namespace App\Entity\Traits;

trait MerchandiseTotalsTrait
{
    public function totalEnterAmount()
    {
        return $this->calculateTotal(function ($merchandise) {
            return $merchandise->getTotalEnterValue();
        });
    }

    public function totalExitAmount()
    {
        return $this->calculateTotal(function ($merchandise) {
            return $merchandise->getTotalExitValue();
        });
    }

    public function totalGrossProfit()
    {
        return $this->totalExitAmount() - $this->totalEnterAmount();
    }

    public function totalGrossProfitPercent()
    {
        return $this->totalGrossProfit() / $this->totalEnterAmount() * 100;
    }

    private function calculateTotal($func)
    {
        $total = 0;

        foreach ($this->merchandises as $merchandise)
        {
            $total += $func($merchandise);
        }

        return $total;
    }
}
