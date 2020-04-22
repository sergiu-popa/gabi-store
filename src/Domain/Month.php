<?php

namespace App\Domain;

use App\Entity\Merchandise;

class Month
{
    private $days;

    // TODO calculate todals when adding days

    public function __construct($year, $month)
    {
        $date = new \DateTime("$year-$month-01");
        $lastDay = $date->format('t');

        // For current month of this year, show today as the last day
        if ($year === date('Y') && $month === date('m')) {
            $lastDay = date('j');
        }

        for ($i = 1; $i <= $lastDay; $i++) {
            $this->days[$i] = new Day();
        }
    }

    public function addItemsInEachDay($property, $items)
    {
        foreach ($items as $item) {
            $day = $item->getDate()->format('j');
            $setter = 'add' . ucfirst($property);

            $this->days[$day]->$setter($item);
        }
    }

    /**
     * @return array []Day
     */
    public function getDays(): array
    {
        return $this->days;
    }
}
