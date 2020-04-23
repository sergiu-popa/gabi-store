<?php

namespace App\Domain;

class Month
{
    /** @var Day[] */
    private $days;

    public function __construct($year, $month)
    {
        $this->days = self::generateDays($year, $month);
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
     * @return Day[]
     */
    public function getDays(): array
    {
        return $this->days;
    }

    public static function generateDays($year, $month, $class = Day::class)
    {
        $days = [];
        $date = new \DateTime("$year-$month-01");
        $lastDay = $date->format('t');

        // For current month of this year, show today as the last day
        if ($year === date('Y') && $month === date('m')) {
            $lastDay = date('j');
        }

        for ($i = 1; $i <= $lastDay; $i++) {
            $days[$i] = new $class();
        }

        return $days;
    }
}
