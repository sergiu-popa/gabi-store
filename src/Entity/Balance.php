<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\DateTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BalanceRepository")
 */
class Balance implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use DeletedAtTrait;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function jsonSerialize()
    {
        return [
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d')
        ];
    }
}
