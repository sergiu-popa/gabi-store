<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BalanceRepository")
 */
class Balance
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
}
