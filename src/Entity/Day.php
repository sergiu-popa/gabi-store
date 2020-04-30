<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\CreatedByTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DayRepository")
 */
class Day
{
    use IdTrait;
    use DateTrait;
    use CreatedAtTrait;
    use CreatedByTrait;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    // TODO 50 & 100 bils fields (small int unsigned)
}
