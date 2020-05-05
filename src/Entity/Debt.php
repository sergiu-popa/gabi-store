<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DebtRepository")
 */
class Debt implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use NameTrait;
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
            'nume' => $this->name,
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d'),
        ];
    }
}
